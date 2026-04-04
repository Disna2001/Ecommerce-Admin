<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting to PayHere</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto flex min-h-screen max-w-3xl items-center justify-center px-6 py-12">
        <div class="w-full rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                    <i class="fas fa-bolt text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Secure Handoff</p>
                    <h1 class="mt-1 text-2xl font-black">Redirecting to PayHere</h1>
                </div>
            </div>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm text-slate-600">Order <span class="font-semibold text-slate-900">{{ $order->order_number }}</span> is ready for online payment.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-white px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Amount</p>
                        <p class="mt-2 text-lg font-black text-slate-900">Rs {{ number_format((float) $order->total, 2) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Mode</p>
                        <p class="mt-2 text-lg font-black text-slate-900">{{ $sandboxEnabled ? 'Sandbox' : 'Live' }}</p>
                    </div>
                    <div class="rounded-2xl bg-white px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Status</p>
                        <p class="mt-2 text-lg font-black text-slate-900">Awaiting payment</p>
                    </div>
                </div>
            </div>

            @if(!$notifyUrlLooksPublic)
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                    PayHere can open the checkout page from Herd, but the final payment confirmation callback needs a public URL.
                    Set a public `app_public_url` before expecting automatic paid status updates locally.
                </div>
            @endif

            <form id="payhere-checkout-form" method="POST" action="{{ $actionUrl }}" class="mt-6">
                @foreach($payload as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white">
                    Continue to PayHere
                </button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">If the redirect does not start automatically, use the button above.</p>
        </div>
    </div>

    <script>
        window.setTimeout(function () {
            document.getElementById('payhere-checkout-form')?.submit();
        }, 900);
    </script>
</body>
</html>
