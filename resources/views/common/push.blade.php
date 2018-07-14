@if (Auth::check())
    <script src="https://js.pusher.com/2.2/pusher.min.js"></script>
    <script>
        // Simple helper function to generate messages
        function addNotification(data) {
            $('<div class="fade in out alert alert-' + data.type + '"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div>').appendTo("#notification-holder");
        }

        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            encrypted: true,
            authEndpoint: '/push/auth'
        });

        // Subscribe to the public notifications channel
        var serviceChannel = pusher.subscribe('public');
        serviceChannel.bind('notification', addNotification);

        // Also subscribe to a user notification channel
        var userChannel = pusher.subscribe('private-user-{{ Auth::user()->id }}');
        userChannel.bind('notification', addNotification);
    </script>
@endif