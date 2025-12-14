<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-screen flex-col gap-6 p-6 md:p-10">
            <div class="w-full max-w-4xl">
                <div class="flex flex-col gap-6 mt-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
