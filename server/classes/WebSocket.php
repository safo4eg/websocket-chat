<?php
class WebSocket
{
    public static function checkPayloadType($frame)
    {
        $typeInDec = ['close' => 8, 'text' => 1];

        $firstByteToBits = sprintf('%08b', ord($frame[0]));
        $opcodeInDec = bindec(substr($firstByteToBits, 4));

        foreach ($typeInDec as $key => $value) {
            if ($value === $opcodeInDec) {
                return $key;
            }
        }
    }

    public static function encode($text)
    {
        $frameStart = [];
        $payloadLength = strlen($text);

        $frameStart[] = '1000' . sprintf('%04b', hexdec('0x1'));
        $mask = '0';

        if ($payloadLength <= 125) {
            $frameStart[] = $mask . sprintf('%07b', $payloadLength);
        } elseif ($payloadLength <= pow(2, 16)) {
            $frameStart[] = $mask . sprintf('%07b', 126);
            $payloadLengthBin = sprintf('%016b', $payloadLength);
            $payloadLengthBinForBytesArray = str_split($payloadLengthBin, 8);
            foreach ($payloadLengthBinForBytesArray as $byte) {
                $frameStart[] = $byte;
            }
        } elseif ($payloadLength <= pow(2, 64)) {
            $frameStart[] = $mask . sprintf('%07b', 127);
            $payloadLengthBin = sprintf('%064b', $payloadLength);
            $payloadLengthBinForBytesArray = str_split($payloadLengthBin, 8);
            foreach ($payloadLengthBinForBytesArray as $byte) {
                $frameStart[] = $byte;
            }
        }

        $frameResult = '';
        $frameStartLength = count($frameStart);
        for ($i = 0; $i < $frameStartLength; $i++) {
            $frameStart[$i] = chr(bindec($frameStart[$i]));
            $frameResult .= $frameStart[$i];
        }

        return trim($frameResult . $text);
    }

    public static function decode($frame)
    {
        $firstByteToBits = sprintf('%08b', ord($frame[0]));
        $secondByteToBits = sprintf('%08b', ord($frame[1]));

        $opcode = bindec(substr($firstByteToBits, 4)); // не нужен, тк буду считать что пользователи отправляют только текст
        $bodyLength = bindec(substr($secondByteToBits, 1));

        $maskKeyBytesSymbols = null;
        $bodySymbols = null;


        if ($bodyLength < 126) {
            $bodyLength = $bodyLength;
            $maskKeyBytesSymbols = substr($frame, 2, 4);
            $bodySymbols = substr($frame, 6, $bodyLength);
        } else if ($bodyLength == 126) {
            $bodyLengthBytesSymbols = substr($frame, 2, 2);
            $bodyLength = bindec(self::fromSymbolsToBitsStr($bodyLengthBytesSymbols));
            $maskKeyBytesSymbols = substr($frame, 4, 4);
            $bodySymbols = substr($frame, 8, $bodyLength);
        } else {
            $bodyLengthBytesSymbols = substr($frame, 2, 8);
            $bodyLength = bindec(self::fromSymbolsToBitsStr($bodyLengthBytesSymbols));
            $maskKeyBytesSymbols = substr($frame, 10, 4);
            $bodySymbols = substr($frame, 14, $bodyLength);
        }

        $maskKeyBits = self::fromSymbolsToBitsStr($maskKeyBytesSymbols);
        $bodyBits = self::fromSymbolsToBitsStr($bodySymbols);

        $i = 0;
        $unmaskedBody = '';
        while ($i < $bodyLength / 4) {
            $bufferMaskKey = $maskKeyBits;
            $maskedPart = substr($bodyBits, $i * 32, 32);
            $maskedPartLength = strlen($maskedPart);

            if ($maskedPartLength < 32) $bufferMaskKey = substr($maskKeyBits, 0, $maskedPartLength);
            $unmaskedBody .= self::fromBitsStrToSymbols($maskedPart, $maskedPartLength) ^ self::fromBitsStrToSymbols($bufferMaskKey, $maskedPartLength);
            $i++;
        }

        return $unmaskedBody;
    }

    private static function fromSymbolsToBitsStr($symbols)
    {
        $bitsStr = '';
        $symbols = str_split($symbols, 1);
        foreach ($symbols as $symbol) $bitsStr .= sprintf('%08b', ord($symbol));
        return $bitsStr;
    }

    private static function fromBitsStrToSymbols($bitsStr, $amountBits)
    {
        $result = '';
        $iMax = $amountBits / 8;
        for ($i = 0; $i < $iMax; $i++) $result .= chr(bindec(substr($bitsStr, $i * 8, 8)));
        return $result;
    }

    public static function createResponseHeaders($headers)
    {
        $acceptKey = self::createSecWSAcceptKey(self::getSecWSKey($headers));
        $headers = "\r\n\r\nHTTP/1.1 101 Switching Protocols Handshake\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Accept: $acceptKey" . "\r\n\r\n";
        return $headers;
    }

    private static function createSecWSAcceptKey($key)
    {
        $guid = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
        $hash = $key . $guid;
        $hash = sha1($hash, true);
        $hash = base64_encode($hash);
        return $hash;
    }

    private static function getSecWSKey($headers)
    {
        if (!preg_match('#Sec-WebSocket-Key:(?<key>.+)#', $headers, $math)) return 'Sec-WebSocket-Key отсутствует';
        $key = $math['key'];
        return trim($key);
    }
}

