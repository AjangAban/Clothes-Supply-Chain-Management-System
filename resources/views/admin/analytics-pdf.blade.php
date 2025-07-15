<h2>Analytics Report</h2>
@foreach($Sales\ Over\ Time as $section => $rows)
    <h3>{{ is_string($section) ? $section : 'Sales Over Time' }}</h3>
    @if(count($rows) > 0)
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                @foreach(array_keys((array)$rows[0]) as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach((array)$row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No data available.</p>
    @endif
@endforeach
@foreach(['Top Products', 'Inventory', 'Customer Segments'] as $section)
    <h3>{{ $section }}</h3>
    @php $rows = $$section; @endphp
    @if(count($rows) > 0)
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                @foreach(array_keys((array)$rows[0]) as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach((array)$row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No data available.</p>
    @endif
@endforeach 