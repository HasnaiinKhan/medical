Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Image]::FromFile("C:\xampp\htdocs\medical\public\Images\websitecolorsetting.png")
Write-Host "Size: $($img.Width)x$($img.Height)"
$bmp = [System.Drawing.Bitmap]$img
$colors = @{}
for($x=0; $x -lt [Math]::Min($img.Width, 200); $x+=4) {
    for($y=0; $y -lt [Math]::Min($img.Height, 200); $y+=4) {
        $c = $bmp.GetPixel($x,$y)
        $hex = "#{0:X2}{1:X2}{2:X2}" -f $c.R, $c.G, $c.B
        if($colors.ContainsKey($hex)) { $colors[$hex]++ } else { $colors[$hex] = 1 }
    }
}
$colors.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 30 | ForEach-Object {
    Write-Host "$($_.Key): $($_.Value)"
}
$img.Dispose()
