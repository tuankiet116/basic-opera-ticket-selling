import moment from "moment"

export const convertDate = (date: string) => {
    return moment(date).format("DD/MM/yyyy")
}