<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

</head>

<body onload="window.print(); myFunction()">
    <div id="app">
        <h3 style="text-align: center">HÓA ĐƠN BÁN HÀNG</h3>
        <div style="text-align: center; font-size: 18px">Ngày {{$ngay}} tháng {{$thang}} năm {{$nam}}</div>
        <br>
        <div class="line"><strong>1. Đơn hàng </strong>{{$data->ten}}</div>
        <div class="line"><strong>2. Mã đơn hàng: </strong>{{$data->ma}}</div>
        <div class="line"><strong>3. Người mua hàng: </strong>{{$data->user_id ? $data->user->name : 'Khách lẻ'}}</div>
        <div class="line"><strong>4. Phương thức thanh toán </strong>{{$data->thanh_toan}}</div>
        <div class="line"><strong>5. Ghi chú </strong>{{ $data->ghi_chu}}</div>
        <br>
        <table style="border: 2px solid black; border-collapse: collapse; width: 100%">
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
    </div>
</body>
<style>
    table {
        border-collapse: collapse;
    }

    .line {
        margin-bottom: 10px;
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