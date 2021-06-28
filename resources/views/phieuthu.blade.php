<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

</head>

<body onload="window.print(); myFunction()">
    <div id="app" style="font-family: Arial,Helvetica,sans-serif;">
        <div style="display:flex; flex-direction: row;  align-items: center; justify-content: center">
            <div> <img src="http://ruongbacthang.skymapglobal.vn/static/img/logorbt.5dcc5da9.jpg" style="width: 150px"></div>
            <div style="margin-left: 15px">
                <p style="font-size: 16px; font-weight: bold;">RUỘNG BẬC THANG</p>
                <p style="font-size: 14px;">Địa chỉ: 82-84 Ngọc Hân Công Chúa - Ninh Xá - Tp Bắc Ninh</p>
                <p style="font-size: 14px;">SĐT: 0988.861.479 - 0862968081</p>
            </div>
        </div>
        <br>
        <h3 style="text-align: center; font-size: 16px">PHIẾU THU</h3>
        <div style="text-align: center; font-size: 14px">Ngày {{$ngay}} tháng {{$thang}} năm {{$nam}}</div>
        <br>
        <div class="line"><strong>Khách hàng: </strong>{{$data->khachHang ? $data->khachHang->name : '........'}}</div>
        <div class="line"><strong>Thông tin: </strong>{{$data->thong_tin_khach_hang}}</div>
        <div class="line"><strong>Nội dung: </strong> </div>
        <div class="line"><span style="white-space: pre-line">{{$data->noi_dung}}</span></div>
        <div class="line"><strong>Thông tin giao dịch: </strong>{{$data->thong_tin_giao_dich}}</div>
        <br>
        <div class="line"><strong>Tổng tiền: </strong><span id="tongtien">{{ $data->so_tien}}</span></div>
        <div class="line"><strong>Bằng chữ: </strong><span style="font-weight: bold; font-style: italic">{{ $tien }} đồng.</span></div>
        <div style="display:flex; flex-direction: column;  align-items: center; justify-content: center">
                <div>-------------------------------------------------</div>
                <div style="font-size: 11px; font-weight: bold; ">CẢM ƠN QUÝ KHÁCH VÀ HẸN GẶP LẠI!</div>
                <div>website:ruongbacthang.com.vn</div>
        </div>
    </div>
</body>
<style>

    .line {
        margin-bottom: 10px;
        font-size: 14px;
    }

</style>
<script>
    function formatCurrency(n, separate = ".") {
        try {
            if (!n) n = 0;
            var s = parseInt(n).toString();
            var regex = /\B(?=(\d{3})+(?!\d))/g;
            var ret = s.replace(regex, separate);
            return ret;
        } catch (error) {
            return "0";
        }
    }

    function myFunction() {
        document.getElementById("tongtien").innerHTML = formatCurrency(document.getElementById("tongtien").innerHTML) + ' đ';
    }
</script>

</html>