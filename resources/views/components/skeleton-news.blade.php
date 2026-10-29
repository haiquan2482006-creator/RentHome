<!-- Left: Featured Main News Card Skeleton -->
<div class="relative rounded-3xl overflow-hidden min-h-[460px] lg:min-h-[540px] flex flex-col justify-between p-8 sm:p-10 bg-slate-200 border border-slate-200 shadow-card animate-pulse">
    <!-- Top badge placeholders -->
    <div class="flex items-center gap-3">
        <div class="h-7 w-32 bg-slate-300 rounded-full"></div>
        <div class="h-7 w-24 bg-slate-300 rounded-full"></div>
    </div>

    <!-- Bottom content placeholders -->
    <div>
        <div class="h-4 w-24 bg-slate-300 rounded mb-4"></div>
        <div class="h-8 w-11/12 bg-slate-300 rounded mb-3"></div>
        <div class="h-8 w-4/5 bg-slate-300 rounded mb-4"></div>
        <div class="h-4 w-full bg-slate-300/80 rounded mb-2"></div>
        <div class="h-4 w-3/4 bg-slate-300/80 rounded mb-6"></div>
        <div class="h-11 w-36 bg-slate-300 rounded-full"></div>
    </div>
</div>

<!-- Right: 2x2 Grid of 4 Smaller News Skeleton Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-7">
    @for ($i = 0; $i < 4; $i++)
    <div class="relative rounded-3xl overflow-hidden min-h-[255px] flex flex-col justify-between p-7 bg-slate-200 border border-slate-200 shadow-card animate-pulse">
        <!-- Top row -->
        <div class="flex items-center justify-between">
            <div class="h-6 w-20 bg-slate-300 rounded-full"></div>
            <div class="h-4 w-16 bg-slate-300 rounded"></div>
        </div>

        <!-- Bottom content -->
        <div>
            <div class="h-5 w-full bg-slate-300 rounded mb-2"></div>
            <div class="h-5 w-4/5 bg-slate-300 rounded mb-4"></div>
            <div class="flex items-center justify-between pt-2">
                <div class="h-4 w-20 bg-slate-300 rounded"></div>
                <div class="h-4 w-20 bg-slate-300 rounded"></div>
            </div>
        </div>
    </div>
    @endfor
</div>

