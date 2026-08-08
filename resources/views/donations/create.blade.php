@extends('layouts.public')



@section('title', 'Donate to '.$campaign->title)



@section('content')

    @php

        $presetAmounts = \App\Models\Donation::PRESET_AMOUNTS;

        $selectedAmountOption = (string) old('amount_option', '500');

    @endphp



    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <h1 class="text-2xl font-bold mb-2">Donate to</h1>

        <p class="text-lg text-gn-accent font-semibold mb-8">{{ $campaign->title }}</p>



        <form method="POST" action="{{ route('donations.store', $campaign->slug) }}" class="bg-white p-6 rounded-lg border border-gray-200 space-y-6" data-recaptcha="donate">

            @csrf



            <div>

                <label for="donor_name" class="block text-sm font-medium mb-1">Your Name</label>

                <input type="text" name="donor_name" id="donor_name" value="{{ old('donor_name') }}" required

                       class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">

                @error('donor_name')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

            </div>



            <div>

                <label for="donor_email" class="block text-sm font-medium mb-1">Email Address</label>

                <input type="email" name="donor_email" id="donor_email" value="{{ old('donor_email') }}" required

                       class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">

                @error('donor_email')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

            </div>



            <div x-data="{ amountOption: @js($selectedAmountOption) }">

                <span class="block text-sm font-medium mb-2">Amount (₱)</span>

                <div class="grid grid-cols-3 gap-2">

                    @foreach ($presetAmounts as $preset)

                        <label class="cursor-pointer">

                            <input type="radio" name="amount_option" value="{{ $preset }}" x-model="amountOption" class="sr-only peer">

                            <span class="flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md border border-gray-300 peer-checked:border-gn-accent peer-checked:bg-gn-accent/10 peer-checked:text-gn-accent hover:border-gn-accent/60 transition-colors">

                                ₱{{ number_format($preset) }}

                            </span>

                        </label>

                    @endforeach

                    <label class="cursor-pointer">

                        <input type="radio" name="amount_option" value="custom" x-model="amountOption" class="sr-only peer">

                        <span class="flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md border border-gray-300 peer-checked:border-gn-accent peer-checked:bg-gn-accent/10 peer-checked:text-gn-accent hover:border-gn-accent/60 transition-colors">

                            Custom

                        </span>

                    </label>

                </div>

                @error('amount_option')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror



                <div x-show="amountOption === 'custom'" x-cloak class="mt-3">

                    <label for="custom_amount" class="block text-sm font-medium mb-1">Custom amount (₱)</label>

                    <input type="number" name="custom_amount" id="custom_amount" value="{{ old('custom_amount') }}" min="100" step="0.01"

                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">

                    @error('custom_amount')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

                </div>

            </div>



            <div>

                <label for="type" class="block text-sm font-medium mb-1">Donation Type</label>

                <select name="type" id="type" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">

                    @foreach (\App\Models\Donation::TYPES as $value => $label)

                        <option value="{{ $value }}" @selected(old('type', 'one_time') === $value)>{{ $label }}</option>

                    @endforeach

                </select>

                <p class="text-xs text-gn-text/50 mt-1">For monthly plans, enter your <strong>monthly</strong> amount. The full commitment is counted toward the campaign goal immediately.</p>

                @error('type')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

            </div>



            <div>

                <label for="message" class="block text-sm font-medium mb-1">Message <span class="text-gn-text/50 font-normal">(optional)</span></label>

                <textarea name="message" id="message" rows="3" maxlength="1000"

                          class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('message') }}</textarea>

                @error('message')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

            </div>



            <div>

                <label class="flex items-start gap-2 cursor-pointer">

                    <input type="checkbox" name="show_name" value="1" @checked(old('show_name'))

                           class="mt-1 rounded border-gray-300 text-gn-accent focus:ring-gn-accent">

                    <span class="text-sm">Display my name and message on the campaign page as a contributor</span>

                </label>

                @error('show_name')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

            </div>



            @error('g-recaptcha-response')<p class="text-gn-danger text-sm">{{ $message }}</p>@enderror



            <button type="submit" class="w-full px-6 py-3 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">

                Continue to Payment

            </button>



            <p class="text-xs text-gn-text/50 text-center">You will be redirected to our secure payment partner (Xendit).</p>

        </form>



        <a href="{{ route('campaigns.show', $campaign->slug) }}" class="block text-center mt-4 text-gn-accent hover:underline">&larr; Back to campaign</a>

    </div>

@endsection

