<div class="row-fluid">
    <!-- $item is an array with keys, create a table with keys as header and values as rows if $item has sub items repeat it -->
    @if (is_array($item) || is_object($item))
        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach($item as $k => $v)
                        <th>{{ucfirst(str_replace('_', ' ', $k))}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($item as $k => $v)
                        <td>
                            @if (is_array($v) || is_object($v))
                                @include('project.history_item', ['item' => $v])
                            @else
                                {{$v}}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endif
</div>