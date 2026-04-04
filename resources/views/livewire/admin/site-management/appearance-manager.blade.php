<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Storefront Operations</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Site management control center</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Keep branding, homepage content, payment visibility, and storefront identity aligned from one guided workspace.</p>
        </div>
        <button wire:click="saveAll" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
            <span wire:loading.remove wire:target="saveAll,logo_image,favicon_image,hero_image"><i class="fas fa-save"></i> Save Storefront Settings</span>
            <span wire:loading wire:target="saveAll"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
            <span wire:loading wire:target="logo_image,favicon_image,hero_image"><i class="fas fa-cloud-upload-alt fa-bounce"></i> Uploading files...</span>
        </button>
    </div>

    @if($saved)
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm font-medium text-emerald-700">Storefront settings saved successfully.</div>
    @endif

    <section class="admin-surface rounded-[2rem] border border-white/60 bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.18),_transparent_38%),linear-gradient(135deg,rgba(255,255,255,0.96),rgba(248,250,252,0.92))] p-6 shadow-[0_25px_80px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.24),_transparent_30%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(17,24,39,0.92))]">
        <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
            <div class="space-y-5">
                <div class="space-y-3">
                    <span class="inline-flex items-center rounded-full border border-fuchsia-200/70 bg-fuchsia-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-fuchsia-700 dark:border-fuchsia-400/20 dark:bg-fuchsia-400/10 dark:text-fuchsia-200">Customer-Facing Experience</span>
                    <div class="space-y-2">
                        <h3 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Control the full storefront without jumping between tools.</h3>
                        <p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">Update visual identity, homepage copy, category icons, and checkout payment options from one structured admin flow.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Branding</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $storefrontSummary['branding_ready'] ? 'Ready' : 'Review' }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $logo_path ? 'Logo uploaded' : 'Logo missing' }} · {{ $favicon_path ? 'favicon set' : 'favicon missing' }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Hero Block</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $storefrontSummary['hero_ready'] ? 'Live' : 'Edit' }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $hero_image_path ? 'Image loaded' : 'Text-only now' }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Payment Methods</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $storefrontSummary['payments_enabled'] }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Checkout options currently enabled.</p>
                    </div>
                    <div class="rounded-3xl border border-white/70 bg-gradient-to-br from-indigo-500 via-fuchsia-500 to-amber-400 p-4 shadow-lg shadow-indigo-500/20 dark:border-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">Featured Products</p>
                        <p class="mt-3 text-3xl font-black text-white">{{ $storefrontSummary['featured_items'] }}</p>
                        <p class="mt-2 text-sm text-white/80">Products chosen for homepage sections.</p>
                    </div>
                </div>
            </div>

            <aside class="rounded-[1.75rem] border border-white/70 bg-white/85 p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Working Rhythm</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ ucfirst($activeTab) }}</span>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach([['1', 'Branding', 'Upload logo, favicon, and site identity first.'], ['2', 'Homepage', 'Tune trust copy, CTA text, and featured experience.'], ['3', 'Payments', 'Enable only the checkout methods you truly operate.']] as [$step, $title, $text])
                        <div class="flex gap-3 rounded-2xl bg-slate-100/80 p-4 dark:bg-slate-800/80">
                            <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-900 text-xs font-black text-white dark:bg-white dark:text-slate-900">{{ $step }}</div>
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $title }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[270px_minmax(0,1fr)]">
        <aside class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-4 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
            <p class="px-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500">Sections</p>
            <div class="mt-3 space-y-2">
                @foreach(['branding' => ['Branding', 'fa-store'], 'homepage' => ['Homepage', 'fa-house'], 'colors' => ['Colors', 'fa-palette'], 'hero' => ['Hero', 'fa-image'], 'topbar' => ['Top Bar', 'fa-bars'], 'payment' => ['Payments', 'fa-credit-card'], 'categories' => ['Categories', 'fa-table-cells-large'], 'footer' => ['Footer', 'fa-sitemap']] as $tab => [$label, $icon])
                    <button wire:click="$set('activeTab', '{{ $tab }}')" @class(['flex w-full items-center justify-between gap-3 rounded-2xl px-4 py-3 text-left text-sm font-medium transition', 'bg-slate-900 text-white shadow-md shadow-slate-900/10 dark:bg-white dark:text-slate-900' => $activeTab === $tab, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' => $activeTab !== $tab])>
                        <span class="flex items-center gap-3"><i class="fas {{ $icon }} w-4 text-center"></i><span>{{ $label }}</span></span>
                        <span class="text-[11px] font-semibold uppercase tracking-[0.2em] {{ $activeTab === $tab ? 'text-white/70 dark:text-slate-500' : 'text-slate-400 dark:text-slate-500' }}">{{ $tabStats[$tab] ?? '' }}</span>
                    </button>
                @endforeach
            </div>
            <div class="mt-6 rounded-2xl border border-dashed border-slate-200 p-4 dark:border-slate-800">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Operator Notes</p>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                    <li>Save once after editing across multiple tabs.</li>
                    <li>Use Display Items for homepage product rails.</li>
                    <li>Remove old assets here to keep branding clean.</li>
                </ul>
            </div>
        </aside>

        <div class="space-y-6">
            @if($activeTab === 'branding')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Branding foundation</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Define site name, logo, favicon, and the primary storefront identity shown across the public site.</p></div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Site Name</label><input type="text" wire:model="site_name" class="mt-2 w-full rounded-2xl border-slate-200 bg-white text-sm shadow-none focus:border-violet-500 focus:ring-violet-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">@error('site_name') <span class="mt-2 block text-xs text-red-500">{{ $message }}</span> @enderror</div>
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Site Tagline</label><input type="text" wire:model="site_tagline" class="mt-2 w-full rounded-2xl border-slate-200 bg-white text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Site Logo</label>
                            @if($logo_path)<img src="{{ Storage::url($logo_path) }}?v={{ md5($logo_path) }}" class="mt-3 h-16 w-auto rounded-xl bg-white p-2 object-contain shadow-sm"><button type="button" wire:click="removeLogo" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fas fa-trash-alt text-[11px]"></i>Remove logo</button>@endif
                            <input type="file" wire:model="logo_image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
                            <p class="mt-2 text-xs text-slate-400">Recommended: transparent PNG or SVG.</p>
                            <div wire:loading wire:target="logo_image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Uploading logo to temporary storage...
                            </div>
                            @if($logo_image)
                                <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">
                                    <div class="font-semibold">Logo ready to save</div>
                                    <div class="mt-1">{{ $logo_image->getClientOriginalName() }}</div>
                                    <div class="mt-1 text-emerald-600">Press "Save Storefront Settings" to publish the new logo.</div>
                                </div>
                            @elseif($logo_path)
                                <div class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">
                                    Current logo is saved and live.
                                </div>
                            @endif
                            @error('logo_image') <span class="mt-2 block text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Favicon</label>
                            @if($favicon_path)<img src="{{ Storage::url($favicon_path) }}?v={{ md5($favicon_path) }}" class="mt-3 h-12 w-12 rounded-xl bg-white p-2 object-contain shadow-sm"><button type="button" wire:click="removeFavicon" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fas fa-trash-alt text-[11px]"></i>Remove favicon</button>@endif
                            <input type="file" wire:model="favicon_image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
                            <p class="mt-2 text-xs text-slate-400">Recommended: square PNG or ICO.</p>
                            <div wire:loading wire:target="favicon_image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Uploading favicon to temporary storage...
                            </div>
                            @if($favicon_image)
                                <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">
                                    <div class="font-semibold">Favicon ready to save</div>
                                    <div class="mt-1">{{ $favicon_image->getClientOriginalName() }}</div>
                                    <div class="mt-1 text-emerald-600">Press "Save Storefront Settings" to publish the new favicon.</div>
                                </div>
                            @elseif($favicon_path)
                                <div class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">
                                    Current favicon is saved and live.
                                </div>
                            @endif
                            @error('favicon_image') <span class="mt-2 block text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="show_deals_link" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Show Deals in navigation</label>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="show_new_arrivals_link" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Show New Arrivals in navigation</label>
                    </div>
                </div>
            @endif

            @if($activeTab === 'homepage')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Homepage content flow</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tune trust messaging, review copy, search prompt, and the final conversion CTA.</p></div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Utility Row</h4>
                            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Badge Text</label><input type="text" wire:model="utility_badge_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Utility Item 1</label><input type="text" wire:model="utility_left_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Utility Item 2</label><input type="text" wire:model="utility_center_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Search Placeholder</label><input type="text" wire:model="home_search_placeholder" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Trust Features</h4>
                            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div><input type="text" wire:model="feature_one_text" class="w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Feature one"></div>
                                <div><input type="text" wire:model="feature_two_text" class="w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Feature two"></div>
                                <div><input type="text" wire:model="feature_three_text" class="w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Feature three"></div>
                                <div><input type="text" wire:model="feature_four_text" class="w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Feature four"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Reviews Block</h4>
                            <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Section Title</label><input type="text" wire:model="reviews_section_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">Section Subtitle</label><input type="text" wire:model="reviews_section_subtitle" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Final CTA</h4>
                            <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">CTA Title</label><input type="text" wire:model="final_cta_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <label class="mt-4 block text-sm font-medium text-slate-700 dark:text-slate-200">CTA Subtitle</label><input type="text" wire:model="final_cta_subtitle" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Button Text</label><input type="text" wire:model="final_cta_button_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Button Link</label><input type="text" wire:model="final_cta_button_link" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'colors')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Storefront palette</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control the main welcome-page and storefront accent colors used across the public UI.</p></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach([['primary_color', 'Primary'], ['secondary_color', 'Secondary'], ['accent_color', 'Accent'], ['text_color', 'Text'], ['bg_color', 'Background'], ['nav_bg_color', 'Navigation']] as [$field, $label])
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }} Color</label>
                                <div class="mt-3 flex items-center gap-3">
                                    <input type="color" wire:model.live="{{ $field }}" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1">
                                    <input type="text" wire:model="{{ $field }}" class="flex-1 rounded-2xl border-slate-200 text-sm font-mono shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                    <div class="h-10 w-10 rounded-xl border border-slate-200" style="background: {{ $this->$field }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($activeTab === 'hero')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Hero campaign block</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Edit the main hero message, conversion button, and visual asset shown at the top of the welcome page.</p></div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero Title</label><input type="text" wire:model.live="hero_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">@error('hero_title') <span class="mt-2 block text-xs text-red-500">{{ $message }}</span> @enderror</div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero Highlight</label><input type="text" wire:model.live="hero_highlight_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero Microcopy</label><input type="text" wire:model.live="hero_microcopy" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero Subtitle</label><input type="text" wire:model.live="hero_subtitle" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Button Text</label><input type="text" wire:model.live="hero_button_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Button Link</label><input type="text" wire:model="hero_button_link" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Gradient From</label><div class="mt-2 flex items-center gap-3"><input type="color" wire:model.live="hero_bg_from" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model.live="hero_bg_from" class="flex-1 rounded-2xl border-slate-200 text-sm font-mono shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Gradient To</label><div class="mt-2 flex items-center gap-3"><input type="color" wire:model.live="hero_bg_to" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model.live="hero_bg_to" class="flex-1 rounded-2xl border-slate-200 text-sm font-mono shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
                        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero Image</label>
                            @if($hero_image_path)<img src="{{ Storage::url($hero_image_path) }}?v={{ md5($hero_image_path) }}" class="mt-3 h-40 w-full rounded-2xl object-cover"><button type="button" wire:click="removeHeroImage" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fas fa-trash-alt text-[11px]"></i>Remove hero image</button>@endif
                            <input type="file" wire:model="hero_image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
                            <div wire:loading wire:target="hero_image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Uploading hero image to temporary storage...
                            </div>
                            @if($hero_image)
                                <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                                    <div class="font-semibold">Hero image ready to save</div>
                                    <div class="mt-1">{{ $hero_image->getClientOriginalName() }}</div>
                                    <div class="mt-1 text-emerald-600">Press "Save Storefront Settings" to publish the new hero image.</div>
                                    <img src="{{ $hero_image->temporaryUrl() }}" class="mt-3 h-36 w-full rounded-xl object-cover" alt="Hero preview">
                                </div>
                            @elseif($hero_image_path)
                                <div class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">
                                    Current hero image is saved and live.
                                </div>
                            @endif
                            @error('hero_image') <span class="mt-2 block text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'topbar')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Announcement strip</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enable or disable the top utility announcement and control its messaging and gradient colors.</p></div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model.live="topbar_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable top announcement bar</label>
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Announcement Text</label><input type="text" wire:model.live="topbar_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Gradient From</label><div class="mt-2 flex items-center gap-3"><input type="color" wire:model.live="topbar_bg_from" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model.live="topbar_bg_from" class="flex-1 rounded-2xl border-slate-200 text-sm font-mono shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Gradient To</label><div class="mt-2 flex items-center gap-3"><input type="color" wire:model.live="topbar_bg_to" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model.live="topbar_bg_to" class="flex-1 rounded-2xl border-slate-200 text-sm font-mono shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'categories')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Category icon mapping</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose the visual icons used by the storefront category strip so browsing stays easy to scan.</p></div>
                    @php $cats = \App\Models\Category::all(); @endphp
                    <div class="grid gap-3">
                        @forelse($cats as $cat)
                            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:flex-row md:items-center dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-600"><i class="fas {{ $category_icons[$cat->id] ?? 'fa-tag' }}"></i></div>
                                <div class="min-w-[180px] font-medium text-slate-800 dark:text-white">{{ $cat->name }}</div>
                                <select wire:model.live="category_icons.{{ $cat->id }}" class="w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                    <option value="">-- Select Icon --</option>
                                    @foreach($iconOptions as $icon => $label)<option value="{{ $icon }}">{{ $label }} ({{ $icon }})</option>@endforeach
                                </select>
                            </div>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-400">No categories found yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if($activeTab === 'payment')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Checkout payment options</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enable only the payment flows your team can actually verify and support.</p></div>
                    <div class="grid gap-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-center justify-between gap-4"><div><h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Cash on Delivery</h4><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Simple offline payment option for local orders.</p></div><label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300"><input type="checkbox" wire:model="enable_cod" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable</label></div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Label</label><input type="text" wire:model="cod_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Description</label><input type="text" wire:model="cod_description" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-center justify-between gap-4"><div><h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Bank Transfer</h4><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manual review flow with account details and payment slip uploads.</p></div><label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300"><input type="checkbox" wire:model="enable_bank_transfer" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable</label></div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Label</label><input type="text" wire:model="bank_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Description</label><input type="text" wire:model="bank_description" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Instruction Title</label><input type="text" wire:model="bank_instruction_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Bank Name</label><input type="text" wire:model="bank_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Account Name</label><input type="text" wire:model="bank_account_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Account Number</label><input type="text" wire:model="bank_account_number" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Branch</label><input type="text" wire:model="bank_branch" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Instruction Body</label><textarea wire:model="bank_instruction_body" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-center justify-between gap-4"><div><h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Online / Card Payment</h4><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">External gateway or manual online confirmation flow.</p></div><label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300"><input type="checkbox" wire:model="enable_card_payment" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable</label></div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Label</label><input type="text" wire:model="card_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Description</label><input type="text" wire:model="card_description" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Instruction Title</label><input type="text" wire:model="card_instruction_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                                <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Instruction Body</label><textarea wire:model="card_instruction_body" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'footer')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Footer and social identity</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update the footer copy and linked social platforms customers see across the storefront.</p></div>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Footer Tagline</label><input type="text" wire:model="footer_tagline" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Footer Copyright</label><input type="text" wire:model="footer_copyright" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Facebook URL</label><input type="text" wire:model="facebook_url" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Twitter / X URL</label><input type="text" wire:model="twitter_url" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Instagram URL</label><input type="text" wire:model="instagram_url" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Pinterest URL</label><input type="text" wire:model="pinterest_url" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
