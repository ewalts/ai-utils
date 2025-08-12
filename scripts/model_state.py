#!/ai_wrk/hf/bin/python3
############################################################################################
###> New x py -> model_state.py  -> Initial creation user => eric => 2025-03-12_14:32:40 ###
############################################################################################
#_#>

from safetensors.torch import load_model, save_model
#my_model_fn = input("Which model should we load? ")
model_name = 'opt-350m-lora'
my_model_fn = "data/models--ybelkada--opt-350m-lora/snapshots/8b25389244e89ed9103a5b60a759073d0e7b2a47/adapter_model.safetensors"
load_model(model,  my_model_fn)

from toruch import nn
class Model(nn.Module):
    def __init__(self):
        super().__init__()
        self.a = nn.Linear(100,100)
        self.b = self.a
        
    def forward(self, x):
        return sef.b(self.a(x))


model.load_state_dict(torch.load(my_model_fn))
model = Model()
print(model.state_dict())

torch.save(model.state_dict(), "model.bin")

