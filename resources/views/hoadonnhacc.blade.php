<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

</head>

<body onload="window.print(); myFunction()">
    <div id="app">
        <div style="display:flex; flex-direction: row;  align-items: center; justify-content: center">
            <div> <img src="http://ruongbacthang.skymapglobal.vn/static/img/logorbt.5dcc5da9.jpg" style="width: 100px"></div>
            <div style="margin-left: 15px">
                <p style="font-size: 12px; font-weight: bold;">RUỘNG BẬC THANG</p>
                <p style="font-size: 10px;">Địa chỉ: 54 Ngọc Hân Công Chúa - Ninh Xá - Tp Bắc Ninh</p>
                <p style="font-size: 10px;">SĐT: 0988.861.479 - 0862968081</p>
            </div>
        </div>

        <h3 style="text-align: center; font-size: 11px">ĐƠN NHẬP HÀNG</h3>
        <div style="text-align: center; font-size: 9px">Ngày {{$ngay}} tháng {{$thang}} năm {{$nam}}</div>
        <br>
        <div class="line"><strong>1. Đơn hàng </strong>{{$data->ten}}</div>
        <div class="line"><strong>2. Mã đơn hàng: </strong>{{$data->ma}}</div>
        <div class="line"><strong>3. Nhà cung cấp: </strong>{{$data->user_id ? $data->user->name : '......'}}</div>
        <div class="line"><strong>5. Ghi chú </strong>{{ $data->ghi_chu}}</div>
        <br>
        <table style="border: 2px solid black; border-collapse: collapse; width: 100%; font-size: 10px">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên hàng hóa</th>
                    <th>Đơn vị tính</th>
                    <th>Đơn giá (Vnđ)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (Vnđ)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->sanPhams as $key =>$tb)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$tb->sanPham->ten_san_pham}}</td>
                    <td>{{$tb->sanPham->don_vi_tinh}}</td>
                    <td>{{$tb->don_gia}}</td>
                    <td>{{$tb->so_luong}}</td>
                    <td>{{$tb->so_luong * $tb->don_gia}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <div class="line"><strong>Tổng tiền: </strong><span id="tongtien">{{ $data->tong_tien}}</span></div>
        <div class="line"><strong>Bằng chữ: </strong><span style="font-weight: bold; font-style: italic">{{ $tien }} đồng.</span></div>
        <div class="line"><strong>Đã thanh toán:..............................................................</div>
        <div class="line"><strong>Còn phải thanh toán:...................................................</div>
    </div>
</body>
<style>
    table {
        border-collapse: collapse;
    }

    .line {
        margin-bottom: 10px;
        font-size: 10px;
    }

    table,
    th,
    td {
        border: 1px solid black;
        text-align: center;
        height: 28px;
    }

    th {
        height: 40px;
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