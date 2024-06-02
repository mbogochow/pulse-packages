<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Composer Dependencies">
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
        @if (empty($outdated) && empty($audit))
            <x-pulse::no-results />
        @else
            @if (!empty($audit))
                @if (!empty($audit['abandoned']))
                    <h1 class="mt-2">Abandoned Packages</h1>
                    <x-pulse::table>
                        <x-pulse::thead>
                            <tr>
                                <x-pulse::th>Package</x-pulse::th>
                                <x-pulse::th>Replaced-by</x-pulse::th>
                            </tr>
                        </x-pulse::thead>
                        <tbody>
                        @foreach ($audit['abandoned'] as $packageName => $replacedBy)
                            <tr wire:key="{{ $packageName }}">
                                <x-pulse::td>
                                    <code class="text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                        {{ $packageName }}
                                    </code>
                                </x-pulse::td>
                                <x-pulse::td>
                                    <code class="text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                        {{ $replacedBy }}
                                    </code>
                                </x-pulse::td>
                            </tr>
                        @endforeach
                        </tbody>
                    </x-pulse::table>
                @endif
                @if (!empty($audit['advisories']))
                    <h1 class="mt-2">Security Advisories</h1>
                    <x-pulse::table style="table-layout: fixed;">
                        <colgroup>
                            <col style="width: 35%"/>
                            <col style="width: 65%"/>
                        </colgroup>
                        <x-pulse::thead>
                            <tr>
                                <x-pulse::th>Package</x-pulse::th>
                                <x-pulse::th class="text-right">Security Issues</x-pulse::th>
                            </tr>
                        </x-pulse::thead>
                        <tbody>
                        @foreach ($audit['advisories'] as $packageName => $packageIssues)
                            <tr wire:key="{{ $packageName }}">
                                <x-pulse::td class="max-w-[1px]">
                                    <code class="block text-xs text-gray-900 dark:text-gray-100 truncate">
                                        {{ $packageName }}
                                    </code>
                                </x-pulse::td>
                                <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold truncate">
                                    <ul>
                                        @php ($count = 0)
                                        @foreach($packageIssues as $packageIssue)
                                            <li>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    @if ($count++ === 5)
                                                        ...
                                                        @break
                                                    @else
                                                        <a href='{{ $packageIssue['link'] }}'>
                                                            {{ $packageIssue['title'] }}
                                                        </a>
                                                    @endif
                                                </p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </x-pulse::td>
                            </tr>
                        @endforeach
                        </tbody>
                    </x-pulse::table>
                @endif
            @endif
            @if (!empty($outdated))
                @foreach ($outdated as $change => $changePackages)
                    <h1 class="font-bold mt-2">{{ ucfirst($change)  }} Changes</h1>
                    <x-pulse::table>
                        <colgroup>
                            <col style="width: 50%" />
                            <col />
                            <col />
                            @if ($showAge)
                                <col />
                            @endif
                        </colgroup>
                        <x-pulse::thead>
                            <tr>
                                <x-pulse::th>Package</x-pulse::th>
                                <x-pulse::th class="text-right">Installed</x-pulse::th>
                                <x-pulse::th class="text-right">Available</x-pulse::th>
                                @if ($showAge)
                                    <x-pulse::th class="text-right">Release Age</x-pulse::th>
                                @endif
                            </tr>
                        </x-pulse::thead>
                        <tbody>
                        @foreach ($changePackages as $package)
                            <tr class="h-2 first:h-0"></tr>
                            <tr wire:key="{{ $package['name'] }}">
                                <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                    {{ $package['name'] }}
                                </code>
                                    @isset($package['source'])
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate" title="">
                                            <a href='{{ $package['source'] }}'>
                                                {{ str($package['source'])->after('://') }}
                                            </a>
                                        </p>
                                    @endisset
                                </x-pulse::td>
                                <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                    {{ $package['version'] }}
                                </x-pulse::td>
                                <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                    {{ $package['latest'] }}
                                </x-pulse::td>
                                @if ($showAge)
                                    <x-pulse::td date class="text-gray-700 dark:text-gray-300 font-bold">
                                        {{ $package['release-age'] }}
                                    </x-pulse::td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </x-pulse::table>
                @endforeach
            @endif
        @endif
    </x-pulse::scroll>
</x-pulse::card>
