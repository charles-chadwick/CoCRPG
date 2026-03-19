<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-darker-950 pt-12 sm:justify-center sm:pt-0">
        <div class="mb-8 text-center">
            <Link href="/" class="inline-block">
                <h1 class="text-2xl font-bold tracking-widest text-primary-400 uppercase">
                    Call of Cthulhu
                </h1>
                <p class="text-xs text-darker-500 tracking-widest uppercase mt-1">Character Creator</p>
            </Link>
        </div>

        <div class="w-full overflow-hidden rounded-lg border border-darker-700 bg-darker-900 px-8 py-7 shadow-2xl sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
