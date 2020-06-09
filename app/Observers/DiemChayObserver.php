<?php

namespace App\Observers;

use App\CamBienDiemChay;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use App\DiemChay;
use App\LichSuHoatDong;
use OneSignal;

class DiemChayObserver
{

    public function created(DiemChay $model)
    {
        if (isset($model)) {
            $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/../../firebase.json'); // đường dẫn của file json ta vừa tải phía trên
            $firebase           = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->withDatabaseUri('https://pccc-6d1dd.firebaseio.com') //bạn có thẻ lấy project id ở mục project setting > general
                ->create();
            $database = $firebase->getDatabase();

            $userRepository = $database->getReference('diem_chay'); //lấy model .
            $userRepository->push($model);

            //Backup tai khoan google firebase moi//

            // $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/../../firebaseBackup.json'); // đường dẫn của file json ta vừa tải phía trên
            // $firebase           = (new Factory)
            //     ->withServiceAccount($serviceAccount)
            //     ->withDatabaseUri('https://pccc-2187f.firebaseio.com') //bạn có thẻ lấy project id ở mục project setting > general
            //     ->create();
            // $database = $firebase->getDatabase();

            // $userRepository = $database->getReference('diem_chay'); //lấy model .
            // $userRepository->push($model);



            if (!empty($model['toa_nha_id'])) {
                $users = \App\User::where('toa_nha_id', $model['toa_nha_id'])->get();
                foreach ($users as $item) {
                    OneSignal::sendNotificationUsingTags(
                        "Điểm cháy mới!",
                        array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => $item->id]),
                        $url = null,
                        $data = ['type' => 'task_new', 'id' => $model['id']]
                    );
                }
            }
            $user = Auth()->user();
            if ($user) {
                LichSuHoatDong::create([
                    'reference_id' => $model->id,
                    'type' => 'diem_chay',
                    'hanh_dong' => 'created',
                    'user_id' => $user->id,
                    'noi_dung' => 'Tạo điểm cháy'
                ]);
            }
        }
    }

    public function updated(DiemChay $model)
    {


        if (isset($model)) {
            $original = $model->getOriginal();
            if (($original['trang_thai'] == "dang_chay" && $model['trang_thai'] == "canh_bao_sai") || ($original['trang_thai'] != "ket_thuc_xu_ly" && $model['trang_thai'] == "ket_thuc_xu_ly")) {
                $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/../../firebase.json'); // đường dẫn của file json ta vừa tải phía trên
                $firebase = (new Factory)
                    ->withServiceAccount($serviceAccount)
                    ->withDatabaseUri('https://pccc-6d1dd.firebaseio.com') //bạn có thẻ lấy project id ở mục project setting > general
                    ->create();
                $database = $firebase->getDatabase();

                $userRepository = $database->getReference('diem_chay'); //lấy model .
                $data = ($userRepository->getValue());
                foreach ($data as $key => $item) {
                    if ($item['id'] == $model['id']) {
                        $userRepository = $database->getReference('diem_chay/' . $key)->remove();
                        break;
                    }
                }
            }
            $user = Auth()->user();
            if ($user) {
                LichSuHoatDong::create([
                    'reference_id' => $model->id,
                    'type' => 'diem_chay',
                    'hanh_dong' => 'updated',
                    'user_id' => $user->id,
                    'noi_dung' => 'Xử lý cháy, cập nhật trạng thái điểm cháy'
                ]);
            }
        }
    }
}
