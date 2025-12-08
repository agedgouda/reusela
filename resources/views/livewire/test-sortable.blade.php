<div class="p-6 border-2 border-dashed border-green-500 mb-6"
     x-data="{ items: @entangle('fluxTestItems') }"
     x-init="
        new Sortable($el, {
            animation: 150,
            handle: '.handle',
            onEnd: (evt) => {
                let orderedIds = Array.from($el.children).map(c => c.dataset.id)
                console.log('Flux Test: new order', orderedIds)
                $wire.fluxTestReorder(orderedIds)
            }
        })
     "
>
    <h2 class="text-xl font-bold mb-2">Flux 3 Drag Test</h2>

    <template x-for="item in items" :key="item.id">
        <div class="p-3 bg-white border rounded shadow mb-2 handle cursor-move" :data-id="item.id">
            <span x-text="item.label"></span>
        </div>
    </template>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
