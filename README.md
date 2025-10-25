# Nuxt Template

### OS

- Ubuntu 24.04.1 LTS

### Server Requirements

- Postgres (v16.6)
- PHP (v8.3.6) / Python (v3.12.3)

#### Others

- Nodejs (>20.15.1) Prefer LTS Version
- Composer (v2.7.2)
- NPM (v10.8.3)

> [!IMPORTANT]
> The backend utilizes Laravel's [task scheduling](https://laravel.com/docs/11.x/scheduling#running-the-scheduler) which requires a running cron job.
>
> ex.:
> `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`
>
> For `DEV`, running the `php artisan schedule:work` will suffice

### Resources

- [NUXT API Reference](https://nuxt.com/docs/api)
- [NUXT UI](https://ui.nuxt.com/getting-started)
