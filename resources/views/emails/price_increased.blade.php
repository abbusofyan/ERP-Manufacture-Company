<!DOCTYPE html>
<html>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
        <table style="width: 100%; margin: 0 auto; border-collapse: collapse; background-color: #f9f9f9; border: 1px solid #ddd;">
            <tbody>
                <tr>
                    <td colspan="6" style="padding: 20px; font-size: 16px;">
                        <p>Dear <strong>{{ $user }}</strong>,</p>
                        <p>Below is the list of items with increased prices:</p>
                    </td>
                </tr>
                <tr style="background-color: #f1f1f1;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Vendor</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Item ID</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Item Name</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">UOM</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Previous Price</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">New Price</th>
                </tr>
                @foreach ($data as $item)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item['vendor_name'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item['code'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item['item_name'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item['uom'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">${{ $item['price_before'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">${{ $item['price_after'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
