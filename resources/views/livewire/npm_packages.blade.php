<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="NPM Dependencies">
        <x-slot:icon>
            <x-dynamic-component :component="'pulse::icons.sparkles'" />
        </x-slot:icon>
        @if ($time)
            <x-slot:details>
                Last updated: {{ $time }}
            </x-slot:details>
        @endif
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if (empty($outdated))
            <x-pulse::no-results />
        @else
            @foreach ($outdated as $change => $changePackages)
                <h1 class="font-bold mt-2">{{ ucfirst($change)  }} Changes</h1>
                <x-pulse::table>
                    <colgroup>
                        <col style="width: 50%" />
                        <col />
                        <col />
                    </colgroup>
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th>Package</x-pulse::th>
                            <x-pulse::th class="text-right">Installed</x-pulse::th>
                            <x-pulse::th class="text-right">Available</x-pulse::th>
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                    @foreach ($changePackages as $key => $package)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $key }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                    {{ $key }}
                                </code>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate" title="">
                                    <a href='{{ $package['homepage'] }}'>
                                        {{ str($package['homepage'])->after('://') }}
                                    </a>
                                </p>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $package['current'] }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $package['latest'] }}
                            </x-pulse::td>
                        </tr>
                    @endforeach
                    </tbody>
                </x-pulse::table>
            @endforeach
        @endif
    </x-pulse::scroll>
</x-pulse::card>
