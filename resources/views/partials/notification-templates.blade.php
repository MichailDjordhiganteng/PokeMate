@foreach(['success', 'error', 'warning', 'info'] as $notificationType)
    <template id="notification-template-{{ $notificationType }}">
        <x-notification :type="$notificationType" />
    </template>
@endforeach
