import os
import base64

def Path2encode (Path,ext='php'):
    for path,subfolders, files in os.walk(Path):
        for name in files:
            # try:
                if str(name.split('.')[1]) in ext :
                    File_content = open (os.path.join(path) + name,"r+")
                    EncData = ""
                    Data = File_content.readlines()
                    #print(Data)
                    #print(pri)
                    for Lines in Data:
                        #print(Lines)
                        for i in range(0,len(Lines)):
                            #if (Lines[i] != " ") or (Lines[i] != "\\" and Lines[i+1] != "\\"):
                                #print(Lines[i])
                                EncData += chr((ord(Lines[i])+5)^ord('b'))
                    print(base64.b64encode(EncData.encode('utf-8')))
                    # Decoded = ""
                    # for i in range(0,len(EncData.encode('utf-8'))):
                    #     Decoded += str(chr((ord(EncData[i]))^ord('b') - 5 ).encode('utf-8'))
                    # print(Decoded)

                        
            # except:
            #     continue
    return(0)     


Path2encode("C:\\Users\\Milad\\Desktop\\YCSM-Project\\test\\")