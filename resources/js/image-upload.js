function assignFiles(input, files) {
    if (!files?.length) {
        return;
    }

    const dataTransfer = new DataTransfer();

    if (input.multiple) {
        Array.from(files).forEach((file) => dataTransfer.items.add(file));
    } else {
        dataTransfer.items.add(files[0]);
    }

    input.files = dataTransfer.files;
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function updateSelectedLabel(zone, input) {
    const label = zone.querySelector('[data-image-upload-selected]');

    if (!label) {
        return;
    }

    const count = input.files?.length ?? 0;

    if (count === 0) {
        label.textContent = '';
        label.classList.add('hidden');

        return;
    }

    const names = Array.from(input.files).map((file) => file.name);

    label.textContent = input.multiple
        ? `${count} file${count === 1 ? '' : 's'} selected: ${names.join(', ')}`
        : names[0];
    label.classList.remove('hidden');
}

export function initImageUploadZones() {
    document.querySelectorAll('[data-image-upload]').forEach((zone) => {
        const input = zone.querySelector('input[type="file"]');

        if (!input || zone.dataset.imageUploadReady === 'true') {
            return;
        }

        zone.dataset.imageUploadReady = 'true';

        const setDragging = (isDragging) => {
            zone.classList.toggle('gn-image-upload--dragging', isDragging);
        };

        ['dragenter', 'dragover'].forEach((eventName) => {
            zone.addEventListener(eventName, (event) => {
                event.preventDefault();
                setDragging(true);
            });
        });

        zone.addEventListener('dragleave', (event) => {
            event.preventDefault();

            if (!zone.contains(event.relatedTarget)) {
                setDragging(false);
            }
        });

        zone.addEventListener('drop', (event) => {
            event.preventDefault();
            setDragging(false);
            assignFiles(input, event.dataTransfer?.files);
            updateSelectedLabel(zone, input);
        });

        input.addEventListener('change', () => {
            updateSelectedLabel(zone, input);
        });
    });
}
