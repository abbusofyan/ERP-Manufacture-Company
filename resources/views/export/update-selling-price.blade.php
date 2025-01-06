<table>
    <thead>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Current Selling Price</th>
            <th>New Selling Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $product)
            <tr>
                <td>{{$product->sku}}</td>
                <td>{{$product->name}}</td>
                <td>{{$product->selling_price}}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
