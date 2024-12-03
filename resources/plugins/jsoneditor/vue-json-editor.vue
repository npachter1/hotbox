<template>
  <div>
    <div class="jsoneditor-vue"></div>
  </div>
</template>

<script>
    import './assets/jsoneditor.css'
    import JsonEditor from './assets/jsoneditor'
    export default {
        props: ['value','mode','modes'],
        watch: {
            value: {
                immediate: true,
                handler (val)
                {
                    if (!this.internalChange)
                    {
                        this.setEditor(val)
                    }
                },
                deep: true
            }
        },
        
        data () {
            return {
                editor: null,
                error: false,
                json: this.value,
                internalChange: false
            };
        },
        
        async mounted (){
            
            let self = this;
            let defaultMode = 'tree';
            let defaultModes = ['tree', 'code', 'form', 'text', 'view'];

            let options = {
                mode: this.mode,
                modes: this.modes, // allowed modes
                onChange () {
                    try {
                        let json = self.editor.get();
                        self.json = json;
                        self.internalChange = true;
                        self.$emit('input', json);
                        self.$nextTick(function () {
                            self.internalChange = false
                        })
                    } catch (e) {
                        self.$emit('has-error', e);
                    }
                }
            };

            this.editor = await new JsonEditor(this.$el.querySelector('.jsoneditor-vue'), options, this.json);

        },
        
        methods: {
            setEditor(value){
                if(this.editor)
                    this.editor.set(value);
            }
        }
    }
</script>

<style scoped>
  .jsoneditor-vue{
     height:600px;
  }
  .ace_line_group {
    text-align: left;
  }
  .json-editor-container {
    display: flex;
    width: 100%;
  }
  .json-editor-container .tree-mode {
    width: 50%;
  }
  .json-editor-container .code-mode {
    flex-grow: 1;
  }
  .jsoneditor-btns{
    text-align: center;
    margin-top:10px;
  }
  .jsoneditor-vue .jsoneditor-outer{
    min-height:150px;
  }
  .jsoneditor-vue div.jsoneditor-tree{
    min-height: 350px;
  }
  code {
    background-color: #f5f5f5;
  }
</style>
