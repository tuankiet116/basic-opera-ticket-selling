<div>
    <p dir="ltr">
        <strong>Musical Seasons</strong> xin chào bạn,
    </p>
    <p dir="ltr">
        Để hoàn tất thủ tục đặt vé cho
        <strong>Hòa nhạc “{{ $event->name }}” ngày
            {{ date('d-m-Y', strtotime($event->date)) }}</strong>.
        Quý khán giả vui lòng chuyển khoản theo mã QR đính kèm dưới đây.
    </p>
    <p dir="ltr">
        <em>
            To complete your ticket purchase for the {{ $event->name }} {{ date('F j, Y', strtotime($event->date)) }},
            please
            transfer the
            payment as instructed below.
        </em>
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
                                <b style="text-decoration: line-through; color: red; margin-right: 2px;">{{ number_format($seat['price']) }}
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
                        <span
                            style="text-decoration: line-through; color: red; margin-right: 2px;">{{ number_format($price) }}
                            vnd</span>
                        <span>{{ number_format($priceDiscount) }} vnd</span>
                    @else
                        {{ number_format($price) }} vnd
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <p dir="ltr">Tên tài khoản/Beneficiary: <strong>Nguyễn Phương Lan</strong></p>
    <p dir="ltr">Ngân hàng/Bank: <strong>Ngân hàng Việt Nam Thịnh Vượng - VP Bank</strong></p>
    <p dir="ltr">Số tài khoản/Bank account number:<strong> 138373967</strong></p>
    <p dir="ltr">Nội dung chuyển khoản/Syntax:<strong> {{ $event->banking_code . "_Tên bạn_Ghế đã đặt" }} / {{ $event->banking_code . "_Your name_Your seats" }}</strong></p>
    <p dir="ltr"><img
            src="{{ Vite::asset('resources/images/banking.jpg') }}"
            width="602" height="779"></p>
    <p dir="ltr"> </p>
    <p dir="ltr"><strong>Lưu ý: </strong>Ghế sẽ được TẠM GIỮ khi bạn gửi form và chỉ được XÁC NHẬN khi bạn thanh
        toán
        TRONG VÒNG 15 PHÚT sau khi nhận được email này. Sau 15 phút, nếu BTC chưa nhận được thanh toán, ghế sẽ quay trở
        lại
        trạng thái còn trống.</p>
    <p dir="ltr"><em><strong>NOTES: </strong>Seats will only be CONFIRMED once we receive the bank transfer WITHIN
            15
            MINUTES after you receive this email. After 15 minutes, your booked seats will be available on the seat map
            again.</em></p>
    <p dir="ltr"> </p>
    <p dir="ltr">
        - Sau khi chuyển khoản thành công, phản hồi lại email này kèm ảnh màn hình giao dịch thành công.
        Thư xác nhận thanh toán sẽ được gửi về email của bạn trong vòng 24h.</p>
    <p dir="ltr">
        <em>
            - After successful transfer, reply to this email with your successful transaction screenshot.
            Payment confirmation invoice will be sent to your email within 24 hours.
        </em>
    </p>
    <p dir="ltr"> </p>
    <p dir="ltr">Số điện thoại hỗ trợ/Support contact: 082 558 3888</p>
    <p dir="ltr"> </p>
    <p dir="ltr">Xin chân thành cảm ơn / Best regards,</p>
    <strong>
        Musical Seasons<br>
    </strong>
</div>
<div> </div>
<div>
    <table style="width:96.00726%">
        <colgroup>
            <col style="width:26.275992%" width="139">
            <col style="width:73.724008%" width="390">
        </colgroup>
        <tbody>
            <tr>
                <td><br>
                    <p dir="ltr"><img src="{{ Vite::asset('resources/images/image.webp') }}"
                            width="134.86458333333331" height="90"></p>
                </td>
                <td>
                    <p dir="ltr">
                        <strong>HO GUOM OPERA</strong>
                    </p>
                    <a href="https://www.google.com/maps/search/40-40A+Hang+Bai?entry=gmail&amp;source=g"
                        target="_blank" rel="noopener">
                        https://www.google.com/maps/search/40-40A+Hang+Bai?entry=gmail&amp;source=g
                    </a>
                    <p dir="ltr">
                        <strong>Address: </strong>
                        <a href="" target="_blank" rel="noopener">40-40A Hang Bai</a> Str., Hoan Kiem Dist, Hanoi
                    </p>
                    <p dir="ltr">
                        <strong>Email: </strong>
                        <a href="mailto:musicalseasons@hoguomopera.com" target="_blank"
                            rel="noopener">musicalseasons@hoguomopera.com</a>
                    </p>
                    <p dir="ltr">
                        <a href="mailto:live.hoguomopera@gmail.com" target="_blank"
                            rel="noopener">live.hoguomopera@gmail.com</a>
                    </p>
                    <p dir="ltr">
                        <strong>Fanpage:</strong>
                        <a href="https://www.facebook.com/hoguomoperavn" target="_blank" rel="noopener">
                            Ho Guom Opera: Musical Seasons
                        </a>
                    </p>
                    <p dir="ltr">
                        <strong>Hotline: </strong>082 558 3888
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
