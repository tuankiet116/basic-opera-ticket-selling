import moment from "moment"

export const convertDate = (date: string) => {
    return moment(date).format("DD/MM/yyyy")
}

export const convertDateTime = (date: string) => {
    return moment(date).format("DD/MM/yyyy HH:mm:ss")
}