#!/ai_wrk/hf/bin/python3
#######################################################################################
###> New x py -> img_to_txt.py  -> Initial creation user => eric => 2025-05-25_14:57:31
#######################################################################################
#_#>

import os



default_dir = '/ai_wrk/hf/datasets/dell/'
input_path = input("Type new path img_dir,or,y to use" + default_dir)
file_path= '/ai_wrk/hf/datasets/paula/'
if input_path == 'y'or input_path == '':
    img_dir = default_dir
else:
    img_dir = input_path

json_output = file_path + 'dell_metadata.json'
csv_output =  file_path + 'dell_data.csv'
csv_out = ''
my_json_out_file = open(json_output, "w")
my_json_out_file.write("{")
my_json_out_file.close()

files = os.listdir(img_dir)

from transformers import pipeline
OCR = "Salesforce/blip-image-captioning-base" ###> Optical Character Recognision

img_trainers = ["Salesforce/blip-image-captioning-base",
                "nlpconnect/vit-gpt2-image-captioning"
                ]



captioner = pipeline("image-to-text", model="nlpconnect/vit-gpt2-image-captioning")


c = 0
div = ''
for i in files:
    
    n = 0
    img = i
    f = img_dir + img 
    if "dell" in f:
        n = 1
    else:
        n = 0
    
    response = captioner(f)
    csv_out += '"'+ f + '", "' + response[0]["generated_text"] +'"'

    out = div + '"' +str(c) +'": {"image":"'+ f +'" ,"label": "" ,"dell": '+ str(n) +',"text": [{"generated_text": "'+ response[0]["generated_text"] +'"}]}'
    c = c + 1 
    div = ','

    with open(json_output, "a") as my_json_out_file:
        my_json_out_file.write(out)
    my_json_out_file.close()
    print(out)

with open(csv_output, "w") as my_csv_out_file:
     my_csv_out_file.write(csv_out)
my_csv_out_file.close()

with open(json_output, "a") as my_json_out_file:
    my_json_out_file.write("}")

my_json_out_file.close()


