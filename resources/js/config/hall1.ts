import { RowConfig } from "../types/seats"

export const seats1 = [
    {
        row: "A",
        max: 21,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [7, 15, 18, 10]
    },
    {
        row: "B",
        max: 28,
        leftSide: {
            start: 1,
            end: 19
        },
        centerSide: {
            start: 21,
            end: 22
        },
        rightSide: {
            start: 20,
            end: 2
        },
        empty: []
    },
    {
        row: "C",
        max: 29,
        leftSide: {
            start: 1,
            end: 19
        },
        centerSide: {
            start: 21,
            end: 22
        },
        rightSide: {
            start: 20,
            end: 2
        },
        empty: []
    },
    {
        row: "D",
        max: 30,
        leftSide: {
            start: 1,
            end: 19
        },
        centerSide: {
            start: 21,
            end: 20
        },
        rightSide: {
            start: 18,
            end: 2
        },
        empty: []
    },
    {
        row: "E",
        max: 31,
        leftSide: {
            start: 1,
            end: 19
        },
        centerSide: {
            start: 21,
            end: 20
        },
        rightSide: {
            start: 18,
            end: 2
        },
        empty: []
    },
    {
        row: "F",
        max: 30,
        leftSide: {
            start: 1,
            end: 19
        },
        centerSide: {
            start: 21,
            end: 20
        },
        rightSide: {
            start: 18,
            end: 2
        },
        empty: []
    },
    {
        row: "G",
        max: 31,
        leftSide: {
            start: 1,
            end: 19
        },
        centerSide: {
            start: 21,
            end: 20
        },
        rightSide: {
            start: 18,
            end: 2
        },
        empty: []
    }
]

export const seats2: Array<RowConfig> = [
    {
        row: "H",
        max: 28,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [15, 18]
    },
    {
        row: "I",
        max: 27,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [13, 16]
    },
    {
        row: "K",
        max: 28,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [13, 16]
    },
    {
        row: "L",
        max: 27,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [11, 14]
    },
    {
        row: "M",
        max: 28,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [11, 14]
    },
    {
        row: "N",
        max: 27,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [9, 12]
    },
    {
        row: "P",
        max: 28,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [9, 12]
    },
    {
        row: "Q",
        max: 27,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        empty: [7, 10]
    },
    {
        row: "R",
        max: 22,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        walls: [
            {
                start: 21,
                size: 5
            }
        ],
        empty: [7, 10]
    },
    {
        row: "S",
        max: 22,
        leftSide: {
            start: 1,
            end: 15
        },
        centerSide: {
            start: 17,
            end: 18
        },
        rightSide: {
            start: 16,
            end: 2
        },
        walls: [
            {
                start: 21,
                size: 5,
                height: 80,
                marginTop: 30
            }
        ],
        empty: [7, 10]
    },
    {
        row: "T",
        max: 20,
        leftSide: {
            start: 1,
            end: 13
        },
        centerSide: {
            start: 15,
            end: 16
        },
        rightSide: {
            start: 14,
            end: 2
        },
        walls: [
            {
                start: 19,
                size: 5
            }
        ],
        empty: [5, 8]
    },
    {
        row: "U",
        max: 8,
        leftSide: {
            start: 1,
            end: 7
        },
        centerSide: {
        },
        rightSide: {
            start: 8,
            end: 2
        },
        walls: [
            {
                start: 7,
                size: 15
            }
        ],
        empty: [3, 6]
    }
]