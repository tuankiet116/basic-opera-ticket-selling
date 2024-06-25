export type DiscountData = {
    event_id: number,
    discount_code?: string,
    ticket_class_id: number,
    discount_type: "price-discount"|"percentage-discount",
    note: string,
    quantity: number,
    price_discount: number,
    percentage_discount: number,
    start_date: string,
    end_date: string,
}