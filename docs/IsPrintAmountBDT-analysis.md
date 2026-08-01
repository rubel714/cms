# Analysis: "Print Amount in BDT" (`IsPrintAmountBDT`) has no visible effect on PDF / Excel

Date: 2026-08-01
Status: Diagnosed, fix not yet applied (pending a decision on totals behaviour)

## Summary

Unchecking **Print Amount in BDT** appears to do nothing on the generated Bill PDF and Excel.

The flag is saved, sent, and read correctly. The problem is entirely in the two report scripts: the
"Amount in BDT" column is wired up differently from the other four print options. Instead of removing
the column, it only blanks the per-invoice detail cells, while the column header and every totals
figure (Sub Total, Rebate, VAT, Tax, Total) are still printed. The result looks like the option was
ignored.

## Files involved

| File | Role |
| --- | --- |
| `src/views/screens/billgenerate/BillGenerate.js` | UI checkboxes, builds the report query string |
| `backend/source/api/api_pages/billgenerate.php` | Persists / loads the five print flags |
| `backend/report/GenerateBill.php` | PDF generation |
| `backend/report/GenerateBillExcel.php` | Excel generation |

## What is working correctly

The whole pipeline up to the report scripts is fine, so the bug is *not* in the front end or the API.

1. The checkbox writes a numeric `1` / `0` into state:

```649:652:src\views\screens\billgenerate\BillGenerate.js
  const handleChangeCheck = (e) => {
    const { name, checked } = e.target;
    setCurrentRow({ ...currentRow, [name]: checked ? 1 : 0 });
  };
```

2. All five flags are appended to both report URLs:

```65:83:src\views\screens\billgenerate\BillGenerate.js
  const printOptionQueryString = () => {
    return PRINT_OPTION_FIELDS.map(
      (field) => "&" + field + "=" + (currentRow[field] == 1 ? 1 : 0),
    ).join("");
  };

  const PDFGenerate = () => {
    let finalUrl = EXCEL_EXPORT_URL + "report/GenerateBill.php";
    window.open(
      finalUrl + "?BillId=" + currentRow.id + "&LoginUserId=" + UserInfo.UserId + printOptionQueryString() + "&TimeStamp=" + Date.now(),
    );
  };
```

3. `billgenerate.php` saves and returns `IsPrintAmountBDT` on both insert (line 353) and update (line 363).

4. Both report scripts read the parameter (`GenerateBill.php` line 23, `GenerateBillExcel.php` line 24):

```23:23:backend\report\GenerateBill.php
$IsPrintAmountBDT = isset($_REQUEST['IsPrintAmountBDT']) ? (int) $_REQUEST['IsPrintAmountBDT'] : 1;
```

## Root cause

### 1. The BDT column is never hidden

The other four options drive the `visible` key, and any column with `visible => false` is dropped from
the column list entirely:

```184:187:backend\report\GenerateBill.php
    ['key' => 'GeneralDescription17', 'label' => 'Style number', 'width' => 13, 'align' => 'left', 'visible' => ($IsPrintStyle == 1)],
    ['key' => 'OrderNumber', 'label' => 'Order Number', 'width' => 10, 'align' => 'left', 'visible' => ($IsPrintOrderNo == 1)],
    ['key' => 'GeneralDescription14', 'label' => 'Merchandiser Name', 'width' => 13, 'align' => 'left', 'visible' => ($IsPrintMerchandiser == 1)],
    ['key' => 'GeneralDescription20', 'label' => 'Service Type', 'width' => 6, 'align' => 'left', 'visible' => ($IsPrintServiceType == 1)],
```

The BDT column is hardcoded to `visible => true` and uses a separate `hiderows` key instead:

```183:183:backend\report\GenerateBill.php
    ['key' => 'BaseAmount', 'label' => 'Amount in BDT', 'width' => 7, 'align' => 'right', 'visible' => true, 'number' => true, 'hiderows' => ($IsPrintAmountBDT != 1)],
```

`GenerateBillExcel.php` line 123 is identical in intent:

```123:123:backend\report\GenerateBillExcel.php
    ['key' => 'BaseAmount', 'label' => 'Amount in BDT', 'width' => 18, 'visible' => true, 'number' => true, 'hiderows' => ($IsPrintAmountBDT != 1)],
```

Because `visible` is always `true`, the `array_filter` that removes hidden columns never removes this
one, so the **"Amount in BDT" header and its full-width column are always printed**.

### 2. `hiderows` only blanks the detail rows

`hiderows` is honoured in exactly one place per script: the loop that fills invoice detail rows.

PDF:

```219:231:backend\report\GenerateBill.php
foreach ($sqlLoop1result as $result) {
    $values = [];
    foreach ($columns as $col) {
        if (!empty($col['hiderows'])) {
            continue;
        }
        $raw = isset($result[$col['key']]) ? $result[$col['key']] : '';
        $values[$col['key']] = !empty($col['number'])
            ? number_format($raw, 2)
            : htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    }
    $html .= buildRow($columns, $values);
}
```

Excel:

```192:200:backend\report\GenerateBillExcel.php
foreach ($sqlLoop1result as $result) {
    foreach ($columns as $col) {
        if (!empty($col['hiderows'])) {
            continue;
        }
        $sheet->setCellValue($col['letter'] . $dataRow, isset($result[$col['key']]) ? $result[$col['key']] : '');
    }
    $dataRow++;
}
```

### 3. The totals ignore the flag completely

The summary rows write `BaseAmount` unconditionally in both scripts — they never check `hiderows`.

PDF (`GenerateBill.php` lines 233-265) writes `BaseAmount` for Sub Total, Rebate, VAT, Tax and Total.
Excel does the same via `$bdtCol`:

```203:214:backend\report\GenerateBillExcel.php
$labelCol = $colLetter['GeneralDescription11'];
$fcCol = $colLetter['TransactionAmount'];
$bdtCol = $colLetter['BaseAmount'];

if (count($sqlLoop1result) > 0) {
    // Sub Total Row
    $sheet->setCellValue($labelCol . $dataRow, 'Sub Total');
    $sheet->setCellValue($fcCol . $dataRow, $TotalTransactionAmount);
    $sheet->setCellValue($bdtCol . $dataRow, $TotalBaseAmount);
```

### Net observed behaviour

With the box unchecked, the column header, the column width, and all five totals figures still print.
Only the per-invoice BDT cells go blank — which reads as a rendering glitch rather than an applied
option, hence the "not working" report.

## Decision required before fixing

Sub Total, Rebate, VAT, Tax and Total are all BDT figures. If the BDT column is simply removed the way
the other four options work, the bill loses its Total line entirely. So the intended behaviour when the
box is unchecked has to be chosen:

- **Option A — Remove the column completely.** Drop "Amount in BDT" including all totals. The bill then
  shows only FC amounts and has no Total figure.
- **Option B — Hide detail amounts only, keep totals.** Keep the column and its Sub Total / Rebate /
  VAT / Tax / Total figures, blank only the per-invoice rows. This is what the current code was
  reaching for; it just needs the header/label made sensible so it does not look broken.
- **Option C — Remove the column from the table, print totals below it.** Invoice rows carry no BDT
  amount, but the totals block is still rendered underneath the table.

## Implementation notes for whichever option is chosen

- Any fix must be applied to **both** `GenerateBill.php` and `GenerateBillExcel.php`; they duplicate the
  column definitions and totals logic independently.
- For Option A, `$colLetter['BaseAmount']` in `GenerateBillExcel.php` (line 205) becomes undefined once
  the column is filtered out, so the whole summary block must be guarded. The PDF's `buildRow` already
  tolerates missing keys, so it needs no equivalent guard.
- Column widths are rescaled automatically after filtering in both scripts (`pct` in the PDF,
  `Coordinate::stringFromColumnIndex` in Excel), so removing a column will not break the layout.
- Defaults in both scripts are `1` when the parameter is absent, which keeps any older direct links
  rendering the full layout.
