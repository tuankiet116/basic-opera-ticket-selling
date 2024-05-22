<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute phải được chấp nhận.',
    'accepted_if' => ':attribute phải được chấp nhận khi :other là :value.',
    'active_url' => ':attribute phải là 1 URL còn hoạt động.',
    'after' => ':attribute phải là ngày sau ngày :date.',
    'after_or_equal' => ':attribute phải là ngày sau hoặc bằng ngày :date.',
    'alpha' => ':attribute chỉ chứa chữ cái Unicode nằm trong \p{L} và \p{M}.',
    'alpha_dash' => ':attribute chỉ chứa chữ cái Unicode nằm trong \p{L} và \p{M}, số, dấu gạch ngang ASCII, gạch dưới ASCII.',
    'alpha_num' => ':attribute chỉ chứa chữ cái Unicode nằm trong \p{L} và \p{M} và số.',
    'array' => ':attribute phải là dạng mảng.',
    'ascii' => ':attribute chỉ được chứa các ký tự và ký hiệu chữ và số một byte.',
    'before' => ':attribute phải là ngày trước ngày :date.',
    'before_or_equal' => ':attribute phải là ngày trước hoặc bằng ngày :date.',
    'between' => [
        'array' => ':attribute phải nằm giữa :min và :max phần tử.',
        'file' => ':attribute phải nằm giữa :min và :max kilobytes.',
        'numeric' => ':attribute phải nằm giữa :min và :max.',
        'string' => ':attribute phải nằm giữa :min và :max ký tự.',
    ],
    'boolean' => ':attribute phải có giá trị true, false (đúng, sai).',
    'can' => ':attribute chứa giá trị trái phép.',
    'confirmed' => ':attribute xác nhận trường không khớp.',
    'current_password' => 'Mật khẩu không chính xác.',
    'date' => ':attribute phải là 1 ngày hợp lệ.',
    'date_equals' => ':attribute phải là ngày bằng với ngày :date.',
    'date_format' => ':attribute phải khớp với định dạng :format.',
    'decimal' => ':attribute phải có :decimal chữ số thập phân.',
    'declined' => ':attribute phải bị từ chối.',
    'declined_if' => ':attribute phải bị từ chối khi :other là :value.',
    'different' => ':attribute và :other phải khác nhau.',
    'digits' => ':attribute phải là :digits chữ số.',
    'digits_between' => ':attribute phải nằm giữa :min và :max chữ số.',
    'dimensions' => ':attribute không có kích thước hợp lệ.',
    'distinct' => ':attribute có giá trị lặp.',
    'doesnt_end_with' => ':attribute không được kết thúc bằng các giá trị sau: :values.',
    'doesnt_start_with' => ':attribute không được bắt đầu bằng các giá trị sau: :values.',
    'email' => ':attribute phải là email hợp lệ.',
    'ends_with' => ':attribute phải kết thúc bằng các giá trị sau: :values.',
    'enum' => ':attribute được chọn không hợp lệ.',
    'exists' => ':attribute được chọn không hợp lệ.',
    'extensions' => ':attribute phải có các đuôi sau: :values.',
    'file' => ':attribute phải là 1 tệp.',
    'filled' => ':attribute phải có giá trị.',
    'gt' => [
        'array' => ':attribute phải có nhiều hơn :value phần tử.',
        'file' => ':attribute phải có nhiều hơn :value kilobytes.',
        'numeric' => ':attribute phải có nhiều hơn :value.',
        'string' => ':attribute phải có nhiều hơn :value ký tự.',
    ],
    'gte' => [
        'array' => ':attribute phải có :value phần tử hoặc nhiều hơn.',
        'file' => ':attribute phải có nhiều hơn hoặc bằng :value kilobytes.',
        'numeric' => ':attribute phải có nhiều hơn hoặc bằng :value.',
        'string' => ':attribute phải có nhiều hơn hoặc bằng :value ký tự.',
    ],
    'hex_color' => ':attribute phải là mã màu hexadecimal hợp lệ.',
    'image' => ':attribute phải là hình ảnh.',
    'in' => ':attribute được chọn không hợp lệ.',
    'in_array' => ':attribute phải tồn tại trong :other.',
    'integer' => ':attribute phải là số nguyên.',
    'ip' => ':attribute phải là địa chỉ IP hợp lệ.',
    'ipv4' => ':attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6' => ':attribute phải là địa chỉ IPv6 hợp lệ.',
    'json' => ':attribute phải là chuỗi JSON hợp lệ.',
    'list' => ':attribute phải là 1 danh sách.',
    'lowercase' => ':attribute phải được viết thường.',
    'lt' => [
        'array' => ':attribute phải có ít hơn :value phần tử.',
        'file' => ':attribute phải có ít hơn :value kilobytes.',
        'numeric' => ':attribute phải có ít hơn :value.',
        'string' => ':attribute phải có ít hơn :value ký tự.',
    ],
    'lte' => [
        'array' => ':attribute không được nhiều hơn :value phần tử.',
        'file' => ':attribute phải có ít hơn hoặc bằng :value kilobytes.',
        'numeric' => ':attribute phải có ít hơn hoặc bằng :value.',
        'string' => ':attribute phải có ít hơn hoặc bằng :value ký tự.',
    ],
    'mac_address' => ':attribute phải là địa chỉ MAC hợp lệ.',
    'max' => [
        'array' => ':attribute không được nhiều hơn :max phần tử.',
        'file' => ':attribute không được lớn hơn :max kilobytes.',
        'numeric' => ':attribute không được lớn hơn :max.',
        'string' => ':attribute không được lớn hơn :max ký tự.',
    ],
    'max_digits' => ':attribute không được nhiều hơn :max digits.',
    'mimes' => ':attribute phải là tệp có định dạng: :values.',
    'mimetypes' => ':attribute phải là tệp có định dạng: :values.',
    'min' => [
        'array' => ':attribute phải có ít nhất :min phần tử.',
        'file' => ':attribute phải có ít nhất :min kilobytes.',
        'numeric' => ':attribute phải có ít nhất :min.',
        'string' => ':attribute phải có ít nhất :min ký tự.',
    ],
    'min_digits' => ':attribute phải có ít nhất :min chữ số.',
    'missing' => ':attribute phải được bỏ qua.',
    'missing_if' => ':attribute phải được bỏ qua khi :other là :value.',
    'missing_unless' => ':attribute phải được bỏ qua trừ khi :other là :value.',
    'missing_with' => ':attribute phải được bỏ qua khi :values tồn tại.',
    'missing_with_all' => ':attribute phải được bỏ qua khi :values tồn tại.',
    'multiple_of' => ':attribute phải là số nhiều của :value.',
    'not_in' => ':attribute được chọn không hợp lệ.',
    'not_regex' => ':attribute định dạng không hợp lệ.',
    'numeric' => ':attribute phải là 1 số.',
    'password' => [
        'letters' => ':attribute phải chứa ít nhất 1 chữ cái.',
        'mixed' => ':attribute phải chứa ít nhất 1 chữ viết hoa và viết thường.',
        'numbers' => ':attribute phải chứa ít nhất 1 số.',
        'symbols' => ':attribute phải chứa ít nhất 1 ký tự.',
        'uncompromised' => ':attribute được cho là đã bị lộ dữ liệu. Vui lòng chọn :attribute khác.',
    ],
    'present' => ':attribute phải tồn tại.',
    'present_if' => ':attribute phải tồn tại khi :other là :value.',
    'present_unless' => ':attribute phải tồn tại trừ khi :other là :value.',
    'present_with' => ':attribute phải tồn tại khi :values có tồn tại.',
    'present_with_all' => ':attribute phải tồn tại khi :values có tồn tại.',
    'prohibited' => ':attribute bị cấm.',
    'prohibited_if' => ':attribute bị cấm khi :other là :value.',
    'prohibited_unless' => ':attribute bị cấm trừ khi :other có giá trị :values.',
    'prohibits' => ':attribute cấm :other tồn tại.',
    'regex' => ':attribute định dạng không hợp lệ.',
    'required' => ':attribute là bắt buộc.',
    'required_array_keys' => ':attribute phải chứa các mục nhập cho: :values.',
    'required_if' => ':attribute là bắt buộc khi :other là :value.',
    'required_if_accepted' => ':attribute là bắt buộc khi :other được chấp nhận.',
    'required_unless' => ':attribute là bắt buộc trừ khi :other có giá trị :values.',
    'required_with' => ':attribute là bắt buộc khi :values tồn tại.',
    'required_with_all' => ':attribute là bắt buộc khi :values tồn tại.',
    'required_without' => ':attribute là bắt buộc khi :values không tồn tại.',
    'required_without_all' => ':attribute là bắt buộc khi không có :values tồn tại.',
    'same' => ':attribute phải khớp với :other.',
    'size' => [
        'array' => ':attribute phải chứa :size phần tử.',
        'file' => ':attribute phải là :size kilobytes.',
        'numeric' => ':attribute phải là :size.',
        'string' => ':attribute phải là :size ký tự.',
    ],
    'starts_with' => ':attribute phải bắt đầu với 1 trong các giá trị: :values.',
    'string' => ':attribute phải là 1 chuỗi.',
    'timezone' => ':attribute phải là timezone hợp lệ.',
    'unique' => ':attribute đã được chọn.',
    'uploaded' => ':attribute tải lên thất bại.',
    'uppercase' => ':attribute phải được viết hoa.',
    'url' => ':attribute phải là URL hợp lệ.',
    'ulid' => ':attribute phải là ULID hợp lệ.',
    'uuid' => ':attribute phải là UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
    'recapcha' => "Recapcha không chính xác, vui lòng thử lại.",
];
