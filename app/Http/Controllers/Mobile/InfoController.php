<?php

namespace App\Http\Controllers\Mobile;

use App\Scopes\ActiveScope;
use App\WorkCalendar;
use App\WorkSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use Carbon\Carbon;
use App\Checking;
use App\Http\Resources\ReportCheckingResource;
use Validator;

class InfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getCompanyInfo']]);
    }
    public function getCompanyInfo($code)
    {
        $company = Company::where('code', $code)->first();
        if (!$company) return response([
            'message' => 'Mã công ty không đúng'
        ], 400);
        return $company;
    }
    public function getWorkPlaces()
    {
        return auth()->user()->employee->company->workPlaces;
    }

    function getWorkCalendars(Request $request)
    {
        $start_date = Carbon::parse($request->get('start_date'));
        $end_date = Carbon::parse($request->get('end_date'));
        $data = [];
        $employee = auth()->user()->employee;
        $work_schedules = WorkSchedule::query()->where('company_id', $employee->company_id)->get();
        while ($start_date <= ($end_date)) {
            $work_schedule = $work_schedules->where('start_day', "<=", $start_date)->where('end_day', ">=", $start_date)->first();
            if (isset($work_schedule)) {
                $em_work_cal_ids = EmployeeWorkCalendar::query()->where('employee_id', $employee->id)->where('work_schedule_id', $work_schedule->id)->pluck('work_calendar_id');
                $work_cals = WorkCalendar::whereIn('id', $em_work_cal_ids)->get();
                $data_work_cal = [];
                foreach ($work_cals as $work_cal) {
                    $data_work_cal[] = [
                        'diaChi' => $work_cal->workPlace->address,
                        'thoiGian' => 'Từ ' . $work_cal->shiftWork->start_time . ' đến ' . $work_cal->shiftWork->end_time
                    ];
                }
                $data[] = [
                    'date' => $start_date->format('d-m-Y'),
                    'work_calendars' => $data_work_cal
                ];
            } else {
                $data[] = [
                    'date' => $start_date->format('d-m-Y'),
                    'work_calendars' => []
                ];
            }
            $start_date->addDay();
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Thành công',
                'data' => $data
            ],
            200
        );
    }
    public function getWorkCalendar()
    {
        $now = Carbon::now();
        return auth()->user()->employee->workCalendars()->where('date', $now->toDateString())->with('shiftWork')->get();
    }
    public function getCurrentWorkPlace()
    {
        $workCalendar = $this->getWorkCalendar();
        //return $workCalendar;
        $filterWorkCalendar = collect([]);
        $workCalendar->each(function ($item) use (&$filterWorkCalendar) {
            if ($item->shiftWork->end_time > Carbon::now()->toTimeString()) $filterWorkCalendar->push($item);
        });
        if (count($filterWorkCalendar) == 0) return ['data' => null];
        $filterWorkCalendar->each(function ($item) {
            $item['distance'] = Carbon::parse($item->shiftWork->end_time)->diffInSeconds(Carbon::now());
        });
        return $filterWorkCalendar->where('distance', $filterWorkCalendar->min('distance'))[0];
    }

    public function handleImageUpload(Request $request, $action)
    {
        if ($request->image) {
            $image = $request->image;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/' . $action . '/', $name);
            return 'storage/images/' . $action . '/' . $name;
        }
    }

    public function registerEmployee(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'company_code' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => 400,
                    'message' => 'Lỗi validate',
                    'data' => [$validator->errors()->all()]
                ],
                400
            );
        }
        $company = Company::where('code', $data['company_code'])->first();
        if (empty($company)) {
            return response()->json(
                [
                    'code' => 500,
                    'message' => 'Mã công tý không hợp lệ',
                    'data' => []
                ],
                500
            );
        }
        $currentCheckingCode = Employee::query()->where('company_id', $company->id)->withoutGlobalScope(ActiveScope::class)->count();
        $data['checking_code'] =  str_pad((string) ($currentCheckingCode + 1), 4, "0", STR_PAD_LEFT);
        $data['active'] = false;
        $data['company_id'] = $company->id;
        Employee::create($data);
        return [];
    }
    public function reportErrors(Request $request)
    {
        $now = Carbon::now();
        $year = $request->query('year', $now->year);
        $month = $request->query('month', $now->month);
        $checking = auth()->user()->employee->checkings()
            ->where('date_checking', 'like', $year . '-' . sprintf('%02d', $month) . '-%')
            ->with('errors', 'workCalendar.shiftWork')->get();
        $reports = ReportCheckingResource::collection($checking)->groupBy(function ($item) {
            return Carbon::parse($item['date_checking'])->day;
        })->toArray();
        $result = [];
        foreach ($reports as $key => $value) {
            $report = [];
            $report['date'] = $key;
            $report['report_list_day'] = $value;
            $report['warning'] = true;
            foreach ($value  as $shiftWork) {
                if (!$shiftWork['status']) {
                    $report['warning'] = false;
                    break;
                }
            }
            array_push($result, $report);
        }
        return [
            'month' => $month,
            'year' => $year,
            'report_list' => $result
        ];
    }
}
