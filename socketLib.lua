local component = require("component")
local internet = component.internet

socketLib = {}
socketLib.socketConnectAndWrite = function(msg)
    local i = 0
    ::reCon::
    if socket == nil or i <= 2 and i ~= 0 then socket = internet.connect("20.20.20.100:8888") end
    socket.read() 
    socket.read() 
    if socket.read() == nil then
        if i >= 2 then
            socket  = nil
            return nil, "Socket connection init fail"
        end
        i = i + 1
        os.sleep(6)
        goto reCon
    end
    
    i = 0
    repeat
        local bW = socket.write(msg)
        if not bW then
            socket = nil
            return nil, "Err to write msg, connection lost"
        elseif bW > 0 then
            break
        elseif i >= 5 then
            socket = nil
            return nil, "Err to write msg, die timeout"
        end
        i = i + 1
    until bW > 0
    return "", ""
end

socketLib.socketRequest = function(msg)
    local status, errMsg = socketLib.socketConnectAndWrite(msg)
    if status == nil then return status, errMsg end
    local i = 0
    local data = ""
    local bR = ""
    repeat
        bR = socket.read()
        if bR ~= nil and bR ~= "" then
            if string.find(bR, '||||END') ~= nil then
                data = data .. string.gsub(bR, '||||END', "")
                break
            end
            data = data .. bR
        end
        if bR == nil then
            socket = nil
            return nil, "Lost connection while read answer"
        end
        i = i + 1
    until i >= 1000
    if i >= 1000 then
        return nil, "Err to read msg, die timeout"
    else
        socket = nil
        return data
    end
end

return socketLib