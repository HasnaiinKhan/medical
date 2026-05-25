Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Image]::FromFile("C:\xampp\htdocs\medical\public\Images\websitecolorsetting.png")
Write-Host "Size: $($img.Width)x$($img.Height)"
$bmp = [System.Drawing.Bitmap]$img
$colors = @{}
# Sample the full image
for($x=0; $x -lt $img.Width; $x+=2) {
    for($y=0; $y -lt $img.Height; $y+=2) {
        $c = $bmp.GetPixel($x,$y)
        # Skip near-white colors
        if($c.R -gt 240 -and $c.G -gt 240 -and $c.B -gt 240) { continue }
        # Quantize to reduce noise
        $r = [Math]::Round($c.R / 16) * 16
        $g = [Math]::Round($c.G / 16) * 16
        $b = [Math]::Round($c.B / 16) * 16
        $hex = "#{0:X2}{1:X2}{2:X2}" -f $r, $g, $b
        if($colors.ContainsKey($hex)) { $colors[$hex]++ } else { $colors[$hex] = 1 }
    }
}
Write-Host "=== TOP COLORS (excluding near-white) ==="
$colors.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 40 | ForEach-Object {
    Write-Host "$($_.Key): $($_.Value)"
}
$img.Dispose()
