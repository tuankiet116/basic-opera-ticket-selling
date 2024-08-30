## About Hgo-ticket

- Booking ticket depending on event

## How to setup
- Install php version 8.3
- Install php extension intl, mysql, dom, mbstring, curl, zip
- Install crontab and config follow these instructions:
    + ``crontab -e``
    + Change last line to: `* * * * * cd /home/tuankiet/ticket-hgo && php artisan schedule:run >> /dev/null 2>&1`

## Licence

## Author
