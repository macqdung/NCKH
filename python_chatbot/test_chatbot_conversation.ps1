# PowerShell script to test chatbot conversational probing flow with UTF-8 encoding and proper JSON escape handling

$uri = "http://localhost:8000/chatbot"
$headers = @{
    "Content-Type" = "application/json; charset=utf-8"
}

function Send-Message {
    param (
        [string]$message
    )
    $bodyObject = [PSCustomObject]@{
        message = $message
    }
    $body = $bodyObject | ConvertTo-Json -Depth 3
    Write-Host "User: $message"
    try {
        $response = Invoke-RestMethod -Uri $uri -Method POST -Headers $headers -Body $body -ContentType 'application/json'
        Write-Host "Bot: $($response.reply)"
        return $response.reply
    }
    catch {
        Write-Host "Error invoking chatbot: $_"
    }
}

# Start conversation
Send-Message "hello"

# User responds to probing about favorite genre
Send-Message "Tôi thích thể loại khoa học viễn tưởng"

# User responds to probing about mood
Send-Message "Tôi đang rất vui"

# User responds to probing about reading level
Send-Message "Tôi là người mới bắt đầu đọc sách"

# User asks for recommendations
Send-Message "Cảm ơn, tôi muốn một số đề xuất sách"
