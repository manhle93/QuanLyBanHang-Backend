<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

</head>

<body onload="window.print(); myFunction()">
    <div id="app" style="font-family: Arial,Helvetica,sans-serif;">
        <div style="display:flex; flex-direction: row;  align-items: center; justify-content: center">
            <!-- <div> <img src="http://ruongbacthang.skymapglobal.vn/static/img/logorbt.5dcc5da9.jpg" style="width: 100px"></div> -->
            <div style="text-align: center;">
                <div style="font-size: 12px; font-weight: bold;">RUỘNG BẬC THANG</div>
                <div style="font-size: 10px;">Đ/C: 82-84 Ngọc Hân Công Chúa - Ninh Xá - Tp Bắc Ninh</div>
                <br />
                <div style="font-size: 10px;">SĐT: 0988.861.479 - 0862.968.081</div>
                <div style="font-size: 10px;">Techcombank: 19031781986686 - Nguyen Thu Trang</div>
                <div>-------------------------------</div>
            </div>
        </div>
        <div style="text-align: center; font-size: 11px; font-weight: bold">HÓA ĐƠN BÁN HÀNG</div>
        <div style="text-align: center; font-size: 9px"><strong></strong>{{$data->ma}}</div>
        <div style="text-align: center; font-size: 9px; font-size: 8px">( Ngày {{$ngay}} tháng {{$thang}} năm {{$nam}} )</div>
        <br>
        <div class="line"><strong>Người mua hàng: </strong>
            <span>{{$data->user_id && $data->khachHang ? $data->khachHang->ten : 'Khách lẻ'}}</span>
            @if($data->user_id && $data->khachHang)
            <span style="margin-left: 15px;"> - SĐT: {{$data->khachHang->so_dien_thoai}}</span>
            <span style="margin-left: 15px;"> - Đ/C: {{$data->khachHang->dia_chi}}</span>
            @endif
        </div>
        <div class="line"><strong>Đơn hàng </strong>{{$data->ten}}</div>
        <div class="line"><strong>Phương thức thanh toán: </strong>{{$data->thanh_toan == 'tra_sau' ? 'Trả sau' : ($data->thanh_toan == 'tai_khoan' ? 'Tài khoản' : ($data->thanh_toan == 'chuyen_khoan' ? 'Chuyển khoản/Quẹt thẻ' : ($data->thanh_toan == 'tien_mat' ? 'Tiền mặt' : 'Khác')))}}</div>
        <div class="line"><strong>Ghi chú: </strong>{{ $data->ghi_chu}}</div>
        @if($data->nhanVien)
        <div class="line"><strong>Người bán hàng: </strong>{{$data->nhanVien ? $data->nhanVien->name : ''}}</div>
        @endif
        <!-- <div class="line"><strong>Người bán: </strong>{{ $data}}</div> -->
        <table style="border: 1px solid black; border-collapse: collapse; width: 100%; font-size: 10px;">
            <thead>
                <tr>
                    <th>Mặt hàng</th>
                    <th>Đơn giá</th>
                    <th>SL</th>
                    <th>T.Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->sanPhams as $key =>$tb)
                <tr>
                    <td>{{$tb->sanPham->ten_san_pham}}</td>
                    <td><?php
                        $foo =  $tb->gia_ban;
                        echo number_format((float)$foo, 0, ',', '.');
                        ?></td>
                    <td >{{$tb->so_luong}}</td>
                    <td>
                        <?php
                        $foo =  $tb->so_luong * $tb->gia_ban;
                        echo number_format((float)$foo, 0, ',', '.');
                        ?></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <div class="line"><strong>Tổng tiền: </strong><span id="tongtien">{{ $data->tong_tien}} </span><span style="font-weight: bold; font-style: italic"> ( {{ $tien }} đồng. )</span></div>
        <!-- <div class="line"><strong>Bằng chữ: </strong></div> -->
        <div class="line"><strong>Giảm giá: </strong><span id="giamgia">{{ $data->giam_gia}}</span></div>
        <div class="line"><strong>Đã thanh toán: </strong><span id="dathanhtoan">{{ $data->da_thanh_toan}}</span></div>
        <div class="line"><strong>Còn phải thanh toán: </strong><span id="conphaithanhtoan">{{ $data->con_phai_thanh_toan}}</span></div>
        <div style="display:flex; flex-direction: column;  align-items: center; justify-content: center">
            <div>-------------------------------</div>
            <div style="font-size: 11px; font-weight: bold; ">Xin cảm ơn và hẹn gặp lại Quý khách!!!</div>
            <div style="font-style:italic ">website: ruongbacthang.com.vn</div>
        </div>
    </div>
</body>
<style>
    table {
        border-collapse: collapse;
    }

    .line {
        margin-bottom: 3px;
        font-size: 10px;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-style: dotted;
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
        document.getElementsByClassName("tongtien1").innerHTML = formatCurrency(document.getElementsByClassName("tongtien1").innerHTML) + ' đ';
        document.getElementById("giamgia").innerHTML = formatCurrency(document.getElementById("giamgia").innerHTML) + ' đ';
        document.getElementById("dathanhtoan").innerHTML = formatCurrency(document.getElementById("dathanhtoan").innerHTML) + ' đ';
        document.getElementsByClassName("table-giaban").innerHTML = formatCurrency(document.getElementsByClassName("table-giaban").innerHTML)
        console.log(document.getElementById("giaban0").innerHTML)
    }
</script>

</html>