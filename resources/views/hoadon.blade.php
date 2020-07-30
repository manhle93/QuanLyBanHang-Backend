<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

</head>

<body onload="window.print(); myFunction()">
    <div id="app">
        <div style="display:flex; flex-direction: row;  align-items: center; justify-content: center">
            <div> <img src="http://ruongbacthang.skymapglobal.vn/static/img/logorbt.5dcc5da9.jpg" style="width: 80px"></div>
            <div style="margin-left: 20px">
                <p style="font-size: 11px; font-weight: bold;">RUỘNG BẬC THANG</p>
                <p style="font-size: 8px;">Địa chỉ: 54 Ngọc Hân Công Chúa - Ninh Xá - Tp Bắc Ninh</p>
                <p style="font-size: 8px;">SĐT: 0988.861.479 - 0862968081</p>
            </div>
        </div>
        <br>
        <h3 style="text-align: center; font-size: 11px">HÓA ĐƠN BÁN HÀNG</h3>
        <div style="text-align: center; font-size: 9px">Ngày {{$ngay}} tháng {{$thang}} năm {{$nam}}</div>
        <br>
        <div class="line"><strong>Đơn hàng </strong>{{$data->ten}}</div>
        <div class="line"><strong>Mã đơn hàng: </strong>{{$data->ma}}</div>
        <div class="line"><strong>Người mua hàng: </strong>{{$data->user_id ? $data->user->name : 'Khách lẻ'}}</div>
        <div class="line"><strong>Phương thức thanh toán: </strong>{{$data->thanh_toan}}</div>
        <div class="line"><strong>Ghi chú: </strong>{{ $data->ghi_chu}}</div>
        <br>
        <table style="border: 1px solid black; border-collapse: collapse; width: 100%; font-size: 8px">
            <thead>
                <tr>
                    <th>Mặt hàng</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>T.Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->sanPhams as $key =>$tb)
                <tr>
                    <td>{{$tb->sanPham->ten_san_pham}}</td>
                    <td>{{$tb->gia_ban}}</td>
                    <td>{{$tb->so_luong}}</td>
                    <td>{{$tb->so_luong * $tb->gia_ban}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <div class="line"><strong>Tổng tiền: </strong><span id="tongtien">{{ $data->tong_tien}}</span></div>
        <div class="line"><strong>Bằng chữ: </strong><span style="font-weight: bold; font-style: italic">{{ $tien }} đồng.</span></div>
        <div class="line"><strong>Giảm giá: </strong><span id="giamgia">{{ $data->giam_gia}}</span></div>
        <div class="line"><strong>Đã thanh toán: </strong><span id="dathanhtoan">{{ $data->da_thanh_toan}}</span></div>
        <div class="line"><strong>Còn phải thanh toán: </strong><span id="conphaithanhtoan">{{ $data->con_phai_thanh_toan}}</span></div>
        <div style="display:flex; flex-direction: column;  align-items: center; justify-content: center">
                <div>-------------------------------------------------</div>
                <div style="font-size: 11px; font-weight: bold; ">CẢM ƠN QUÝ KHÁCH VÀ HẸN GẶP LẠI!</div>
                <div>website:ruongbacthang.com.vn</div>
        </div>
    </div>
</body>
<style>
    table {
        border-collapse: collapse;
    }

    .line {
        margin-bottom: 10px;
        font-size: 8px;
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
        document.getElementById("conphaithanhtoan").innerHTML = formatCurrency(document.getElementById("conphaithanhtoan").innerHTML) + ' đ';
        document.getElementById("tongtien").innerHTML = formatCurrency(document.getElementById("tongtien").innerHTML) + ' đ';
        document.getElementById("giamgia").innerHTML = formatCurrency(document.getElementById("giamgia").innerHTML) + ' đ';
        document.getElementById("dathanhtoan").innerHTML = formatCurrency(document.getElementById("dathanhtoan").innerHTML) + ' đ';
    }
</script>

</html>