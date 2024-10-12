<div>
    <p>
        <strong>Musical Seasons</strong> xin chào bạn,
    </p>
    <p>
        Để hoàn tất thủ tục đặt vé cho
        <strong>Hòa nhạc “{{ $event->name }}” ngày
            {{ date('d-m-Y', strtotime($event->date)) }}</strong>.
        Quý khán giả vui lòng chuyển khoản theo mã QR đính kèm dưới đây.
    </p>
    <p>
        <i>
            To complete your ticket purchase for the {{ $event->name }} {{ date('F j, Y', strtotime($event->date)) }},
            please
            transfer the
            payment as instructed below.
        </i>
    </p>
    <table style="border-collapse: collapse">
        <colgroup>
            <col width="179">
            <col width="420">
        </colgroup>
        <thead>
            <tr>
                <th style="border: 1px solid black; text-align:center;">
                    <span>Khán phòng / Hall</span>
                </th>
                <th style="border: 1px solid black; text-align:center;">
                    <span>Số ghế / Seat no.</span>
                </th>
                <th style="border: 1px solid black; text-align:center;">
                    <span>Hạng ghế / Ticket Zone</span>
                </th>
                <th style="border: 1px solid black; text-align:center;">
                    <span>Giá vé / Ticket Price</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
            $price = 0;
            $priceDiscount = 0;
            @endphp
            @foreach ($bookings as $hall => $booking)
            @foreach ($booking as $seat)
            <tr>
                <td style="border: 1px solid black; text-align:center;">
                    <b>{{ $hall }}</b>
                </td>
                <td style="border: 1px solid black; text-align:center;">
                    <b>{{ $seat['seat'] }}</b>
                </td>
                <td style="border: 1px solid black; text-align:center;">
                    <b>{{ $seat['class'] }}</b>
                </td>
                <td style="border: 1px solid black; text-align:center;">
                    @if ($seat['price'] > $seat['discount_price'])
                    <b style="text-decoration: line-through; color: red; margin-right: 2px;">{{
                        number_format($seat['price']) }}
                        vnd</b>
                    <b>{{ number_format($seat['discount_price']) }} vnd</b>
                    @else
                    <b>{{ number_format($seat['price']) }} vnd</b>
                    @endif
                </td>
            </tr>
            @php
            $price += $seat['price'];
            $priceDiscount += $seat['discount_price'];
            @endphp
            @endforeach
            @endforeach
            <tr>
                <td style="border: 1px solid black; text-align:center;">Tổng cộng/Total</td>
                <td style="border: 1px solid black; text-align:center;" colspan=3>
                    @if ($seat['price'] > $seat['discount_price'])
                    <span style="text-decoration: line-through; color: red; margin-right: 2px;">{{ number_format($price)
                        }}
                        vnd</span>
                    <span>{{ number_format($priceDiscount) }} vnd</span>
                    @else
                    {{ number_format($price) }} vnd
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <p>Nội dung chuyển khoản/Syntax:<strong> {{ $event->banking_code . "_Tên bạn_Ghế đã đặt" }} / {{
            $event->banking_code . "_Your name_Your seats" }}</strong></p>
    <table style="border-collapse: collapse">
        <thead>
            <tr>
                <td style="border-bottom: solid 1px black; border-right: solid 1px black; padding: 10px;">
                    <p style="margin: 0;">
                        <strong>Số TK cá nhân cho các vé không xuất hoá đơn:</strong>
                        <span style="color: red;">Ưu đãi giảm 5%</span>
                    </p>
                </td>
                <td style="border-bottom: solid 1px black; padding: 10px;">
                    <p style="margin: 0;">
                        <strong>Số TK công ty cho các vé có xuất hoá đơn:</strong>
                    </p>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px; border-right: solid 1px black; padding-right: 20px !important;">
                    <div>
                        <p style="margin: 0;">Tên tài khoản/Beneficiary: <strong>NGUYEN PHUONG LAN</strong></p>
                        <p style="margin: 0;">Số tài khoản/Bank account number:<strong>138373967</strong></p>
                        <p style="margin: 0;">Ngân hàng/Bank: <strong>Ngân hàng Việt Nam Thịnh Vượng - VPBank</strong>
                        </p>
                    </div>
                </td>
                <td style="padding: 10px">
                    <div>
                        <p style="margin: 0;">Tên tài khoản/Beneficiary: <strong>CT CP NHAT MINH SHD</strong></p>
                        <p style="margin: 0;">Số tài khoản/Bank account number:<strong>1101416666</strong></p>
                        <p style="margin: 0;">Ngân hàng/Bank: <strong>Ngân hàng TMCP Ngoại Thương Việt Nam -
                                Vietcombank</strong></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="border-right: solid 1px black; padding-right: 20px !important;">
                    <p><img src="{{ Vite::asset('resources/images/banking_1.png') }}" width="400"></p>
                </td>
                <td>
                    <p><img src="{{ Vite::asset('resources/images/banking.jpg') }}" width="602" height="779"></p>
                </td>
            </tr>
        </tbody>
    </table>

    <br />
    <p><strong>Lưu ý: </strong>Ghế sẽ được TẠM GIỮ khi bạn gửi form và chỉ được XÁC NHẬN khi bạn thanh
        toán
        TRONG VÒNG 15 PHÚT sau khi nhận được email này. Sau 15 phút, nếu BTC chưa nhận được thanh toán, ghế sẽ quay trở
        lại
        trạng thái còn trống.</p>
    <p><em><strong>NOTES: </strong>Seats will only be CONFIRMED once we receive the bank transfer WITHIN
            15
            MINUTES after you receive this email. After 15 minutes, your booked seats will be available on the seat map
            again.</em></p>
    <br />
    <p>
        - Sau khi chuyển khoản thành công, phản hồi lại email này kèm ảnh màn hình giao dịch thành công.
        Thư xác nhận thanh toán sẽ được gửi về email của bạn trong vòng 24h.</p>
    <p>
        <em>
            - After successful transfer, reply to this email with your successful transaction screenshot.
            Payment confirmation invoice will be sent to your email within 24 hours.
        </em>
    </p>
    <br />
    <p>Số điện thoại hỗ trợ/Support contact:
        <strong><a href="tel:082 558 3888">082 558 3888</a></strong>
    </p>
    <br />
    <p>Xin chân thành cảm ơn / Best regards,</p>
    <strong>
        Musical Seasons<br>
    </strong>
</div>
<div> </div>
@include("Mail.footer")