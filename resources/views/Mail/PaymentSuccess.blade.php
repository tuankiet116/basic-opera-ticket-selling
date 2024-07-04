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
                2024, at Ho Guom Opera. Below is the payment information for concert ticket(s):
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
            <span>
                Vui lòng đến trước giờ biểu diễn ít nhất 30 phút, xuất trình mã đặt chỗ để nhận vé cứng tại Nhà hát Hồ
                Gươm
                (40-40A Hàng Bài). Đối với những bạn lựa chọn gửi vé theo địa chỉ đăng ký, chúng tôi sẽ liên lạc và
                chuyển vé
                cứng tới địa chỉ đã đăng ký của quý khách trước ngày 22/04/2024. Quý khách vui lòng chi trả theo giá
                ship công
                nghệ khi nhận vé.
            </span>
        </p>
        <p dir="ltr">
            <i>
                Please arrive at least 30 minutes before the performance and present your booking code to receive the
                paper
                ticket(s) at Ho Guom Opera (40-40A Hang Bai). For those who want their tickets delivered to their door,
                we will
                contact and deliver the paper ticket(s) before April 22nd, 2024. Please pay the shipping fee following
                the fee
                on the transportation app when receiving the ticket(s).
            </i>
        </p>
        <p dir="ltr">
            <span>
                Lưu ý về chính sách đổi/trả vé: Chương trình không áp dụng đổi/trả vé sau khi đã xác nhận thanh toán
                thành
                công vì bất kỳ lý do gì. Mọi thắc mắc, xin hãy trao đổi trực tiếp với chúng tôi qua
            </span>
        </p>
        <p dir="ltr">
            <i>
                Ticket exchange and return policy: The ticket(s) cannot be exchanged/ returned after successful payment
                confirmation. If you have any questions, please contact us via:
            </i>
        </p>
        <p dir="ltr">
            <span>Xin chân thành cảm ơn/Best regards,</span>
        </p>
        <p dir="ltr">
            <strong>Musical Seasons<br></strong>
        </p>
        <table>
            <tr>
                <td>
                    <img style="width:121px;height:90px" src="{{ Vite::asset('resources/images/image.webp') }}">
                </td>
                <td>
                    <p dir="ltr"><strong>HO GUOM OPERA</strong></p>
                    <p dir="ltr">
                        <strong>Address:</strong>
                        40-40A Hang Bai Str., Hoan Kiem Dist, Hanoi
                    </p>
                    <p dir="ltr">
                        <span>
                            <strong>Email:</strong> <a href="mailto:musicalseasons@hoguomopera.com"
                                target="_blank">musicalseasons@hoguomopera.com</a>
                        </span>
                    </p>
                    <p dir="ltr">
                        <span>
                            <strong>Fanpage:</strong>
                            <a href="https://www.facebook.com/hoguomoperavn" target="_blank">
                                Ho Guom Opera: Musical Seasons
                            </a>
                        </span>
                    </p>
                    <p dir="ltr">
                        <span><strong>Hotline:</strong> 082 558 3888</span>
                    </p>
                </td>
            </tr>
        </table>
    </span>
</div>
