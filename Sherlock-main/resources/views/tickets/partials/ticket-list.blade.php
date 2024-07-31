@foreach($tickets['tickets'] as $ticket)
    <tr>
        <td>{{ $ticket['id'] }}</td>
        <td>{{ $ticket['subject'] }}</td>
        <td>{{ $ticket['type'] ?? 'Incident' }}</td>
        <td>{{ $ticket['requester'] }}</td>
        <td>{{ $ticket['status'] }}</td>
        <td>{{ $ticket['status'] }}</td>
        <td>{{ $ticket['priority'] }}</td>
        <td>{{ $ticket['created_at'] }}</td>
        <td>{{ $ticket['updated_at'] }}</td>
        <td>{{ $ticket['group'] ?? $ticket['responder'] }}</td>
        <td>{{ $ticket['department'] }}</td>
        <td>{{ $ticket['category'] ?? 'N/A' }}</td>
        <td>{{ $ticket['sub_category'] ?? 'N/A' }}</td>
    </tr>
@endforeach
