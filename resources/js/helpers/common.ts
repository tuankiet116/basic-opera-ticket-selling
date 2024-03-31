export const range = (start: number, stop: number, step: number = 1) =>
    Array.from({ length: (stop - start) / step + 1 }, (_, i) => start + i * step);

export const sortByEvenOdd = (numbers: Array<number>) => {
    return numbers.sort((a: number, b: number) => {
        if (a % 2 != 0 && b % 2 != 0) {
            if (a > b) return 1;
            else return -1;
        }
        if (a % 2 == 0 && b % 2 == 0) {
            if (a > b) return -1;
            else return 1;
        }
        if (a % 2 == 0 && b % 2 != 0) return 1;
        if (a % 2 != 0 && b % 2 == 0) return -1;
        return 0;
    });
}