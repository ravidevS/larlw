
<div>
    <form
        wire:submit="changeGreeting()"
    >
        <div class="mt-2 flex gap-4">
            <select
                class="p-4 border rounded-md bg-gray-700 text-white"
                wire:model.live="greeting"
            >
                @foreach ($greetings as $gitem)
                    <option
                        wire:key="greeting-{{ $gitem->id }}"
                        value="{{ $gitem->greeting }}"
                    >
                        {{ $gitem->greeting }}
                    </option>
                @endforeach
            </select>
            <input
                type="text"
                class="p-4 border rounded-md bg-gray-700 text-white"
                wire:model="name"
            >
        </div>
        <div class="mt-2">
            <button
                type="submit"
                class="text-white font-medium rounded-md px-4 py-2 bg-blue-600 hover:bg-blue-700"
            >
                Greet
            </button>
        </div>
    </form>
    @if ($greetingMessage !== '')
    <div>
        {{$greetingMessage}}!
    </div>
    @endif
</div>
