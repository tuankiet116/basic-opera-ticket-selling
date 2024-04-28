import { Popover, Tooltip } from "bootstrap"

export const tooltip = {
    mounted(el) {
        const tooltip = new Tooltip(el)
    }
}

export const popover = {
    mounted(el) {
        const popover = new Popover(el)
    }
}