export function numberWithCommas(x: number) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

export function regexPhoneNumberVietNam(phone: string) {
    const regexPhoneNumber = /(84|0[3|5|7|8|9])+([0-9]{8})\b/g;
    return phone.match(regexPhoneNumber) ? true : false;
}

/** 
 * @param timeString must be a valid format mm:ss
 */
export function calcTimeRemaining(timeString: string): number {
    let timeSplit = timeString.split(":");
    return Number(timeSplit[0]) * 60 * 1000 + Number(timeSplit[1]) * 1000;
}