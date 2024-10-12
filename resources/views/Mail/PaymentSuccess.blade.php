<div>
    <span>
        <p dir="ltr">
            <span>
                <strong>Musical Seasons</strong> xin chào bạn!
            </span>
        </p>
        <p dir="ltr">
            <span>
                Chúc mừng bạn đã hoàn thành thanh toán đặt vé cho Hòa nhạc “{{ $event->name }}” ngày
                {{ date('d-m-Y', strtotime($event->date)) }} tại
                Nhà hát Hồ
                Gươm. Chúng mình gửi lại bạn thông tin thanh toán vé hòa nhạc dưới đây:
            </span>
        </p>
        <p dir="ltr">
            <i>
                Congratulations! You have completed payment to book ticket(s) for the “{{ $event->name }}” Concert on
                {{ date('F j, Y', strtotime($event->date)) }},
                at Ho Guom Opera. Below is the payment information for concert ticket(s):
            </i>
        </p>
        <div>
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
                            <span style="text-decoration: line-through; color: red; margin-right: 2px;">
                                {{ number_format($seat['price']) }} vnd</span>
                            <span>{{ number_format($seat['discount_price']) }} vnd</span>
                            @else
                            {{ number_format($seat['price']) }} vnd
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
                            @if ($price > $priceDiscount)
                            <span style="text-decoration: line-through; color: red; margin-right: 2px;">
                                {{ number_format($price) }} vnd
                            </span>
                            <span>{{ number_format($priceDiscount) }} vnd</span>
                            @else
                            {{ number_format($price) }} vnd
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p dir="ltr">
            <strong>
                Chúng tôi sẽ liên hệ trực tiếp khách hàng về việc nhận vé cứng tại Nhà hát Hồ Gươm (40-40A Hàng Bài)
                hoặc nhận vé theo địa chỉ đã đăng ký.
            </strong>
            <span>
                Nhà hát sẽ gửi chuyển phát qua bưu điện, Quý khách vui lòng thanh toán phí ship khi nhận vé.
            </span>
        </p>
        <p dir="ltr">
            <i>
                <strong>
                    We will contact directly you about either receiving tickets at Ho Guom Opera (40-40A Hang Bai) or
                    receiving tickets at the registered.
                </strong>
                The theater will send them via post office, please pay the shipping fee when receiving the tickets.
            </i>
        </p>
        <p dir="ltr">
            <span>
                Lưu ý về chính sách đổi/trả vé: Chương trình không áp dụng đổi/trả vé sau khi đã xác nhận thanh toán
                thành công vì bất kỳ lý do gì.
                Mọi thắc mắc, xin hãy trao đổi trực tiếp với chúng tôi qua
                Hotline: 088.801.1280 hoặc 082.558.3888
            </span>
        </p>
        <p dir="ltr">
            <i>
                Ticket exchange and return policy: The ticket(s) cannot be exchanged/ returned after successful payment
                confirmation. If you have any questions, please contact us via hotline: 088 801 1280 hoặc 082 558 3888
            </i>
        </p>
        <p dir="ltr">
            <span>Xin chân thành cảm ơn/Best regards,</span>
        </p>
        <p dir="ltr">
            <strong>Musical Seasons<br></strong>
        </p>
    </span>
    @include("Mail.footer")
</div>