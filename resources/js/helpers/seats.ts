import { RowConfig, SeatFormatted, SideConfig } from "../types/seats";
import { range, sortByEvenOdd } from "./common";

const widthSeat = 30;
const heightSeat = 30;
const marginStep = 5;
export const renderSeatsBySide = (seatsSorted: Array<number>, rowConfig: RowConfig, side: string, startMargin: number = 0) => {
    let sideConfig: SideConfig | undefined = { start: 0, end: 0 };

    switch (side) {
        case "center":
            sideConfig = rowConfig.centerSide;
            break;
        case "left":
            sideConfig = rowConfig.leftSide;
            break;
        case "right":
            sideConfig = rowConfig.rightSide;
            break;
    }

    let seatsFormatted: Array<SeatFormatted> = [];
    let seatsRangeSide = seatsSorted.slice(
        seatsSorted.findIndex(v => v == sideConfig?.start),
        seatsSorted.findIndex(v => v == sideConfig?.end) + 1,
    )

    for (let i = 0; i < seatsRangeSide.length; i++) {
        let seat = seatsRangeSide[i];
        let wallSetting = rowConfig.walls?.find(w => w.start == seat);

        seatsFormatted.push({
            id: `${rowConfig.row}${seat}`,
            name: `${rowConfig.row}${seat}`,
            marginTop: startMargin,
            width: widthSeat,
            height: heightSeat
        });

        if (rowConfig.empty?.includes(seat)) {
            seatsFormatted.push({
                id: null,
                name: null,
                marginTop: startMargin,
                width: widthSeat,
                height: heightSeat
            });
        } else if (wallSetting) {
            seatsFormatted.push({
                marginTop: wallSetting.marginTop ?? startMargin,
                isWall: true,
                width: wallSetting.size * widthSeat,
                height: wallSetting.height ?? heightSeat * 2.2
            });
        }
        switch (side) {
            case "left":
                startMargin += marginStep;
                break;
            case "right":
                startMargin -= marginStep;
                break;
        }
    }
    return seatsFormatted;
}

export const renderSeats = (seatsConfig: Array<RowConfig>) => {
    return seatsConfig.map((row) => {
        let startNumberOfRow = (row.leftSide?.start ?? 1) > (row.rightSide?.end ?? 1) ? (row.rightSide?.end ?? 1) : (row.leftSide?.start ?? 1)
        let seats = range(startNumberOfRow, row.max, 1);
        seats = sortByEvenOdd(seats);
        let sideLeft = renderSeatsBySide(seats, row, "left", row.startMargin ?? 0);
        let sideCenter = renderSeatsBySide(seats, row, "center", getLastSideSeatMarginTop(sideLeft) + marginStep);
        let sideRight = renderSeatsBySide(seats, row, "right",
            getLastSideSeatMarginTop(sideCenter) ? getLastSideSeatMarginTop(sideCenter) - marginStep : getLastSideSeatMarginTop(sideLeft));

        sideLeft.push(...sideCenter);
        sideLeft.push(...sideRight);
        return sideLeft
    });
}

export const getLastSideSeatMarginTop = (sideSeats: SeatFormatted[]) => {
    let marginTop: number = 0;
    for (let i = sideSeats.length - 1; i >= 0; i--) {
        if (!sideSeats[i].isWall) {
            marginTop = sideSeats[i].marginTop ?? 0;
            break;
        }
    }
    return marginTop;
}