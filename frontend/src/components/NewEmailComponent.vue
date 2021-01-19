<template>
  <div>
    <md-dialog :md-active.sync="showDialog">
      <md-dialog-title>Send new email</md-dialog-title>

      <md-dialog-content>
        <md-field>
          <label>Mail to</label>
          <md-input v-model="inputMailTo" required></md-input>
        </md-field>
        <md-field>
          <label>Title</label>
          <md-input v-model="inputTitle" required></md-input>
        </md-field>
        <md-field>
          <label>Body</label>
          <md-textarea v-model="inputBody" maxlength="255" required></md-textarea>
        </md-field>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="hideDialog">Close</md-button>
        <md-button class="md-primary" @click="addNewEmailAPI">Send</md-button>
      </md-dialog-actions>
    </md-dialog>

    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showMessageSuccessful" md-persistent>
      <span>Email sent successful!</span>
    </md-snackbar>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showMessageFailed" md-persistent>
      <span>Try again!</span>
    </md-snackbar>
  </div>
</template>

<script>
export default {
name: "NewEmailComponent",
  props:['showDialog'],
  data:()=>({
    showMessageSuccessful:false,
    showMessageFailed:false,
    inputMailTo:null,
    inputTitle:'',
    inputBody:''
  }),
  methods:{
    addNewEmailAPI(){
        const data = {
          mail_to:this.inputMailTo,
          title:this.inputTitle,
          body:this.inputBody
        };
        this.axios.post('http://localhost:8000/mails',data).then(response=>{
          let newEmail = response.data;
          newEmail.is_selected= false;
          newEmail.first_letter = newEmail.mail_to.substr(0,1).toUpperCase();
          this.hideDialog();
          this.showMessageSuccessful = true;
          this.$emit('addNewEmail',newEmail);
        }).catch(error=>{
          console.log(error);
          this.showMessageFailed = true;
        }).then(()=>{

        });
    },
    hideDialog(){
      this.$emit('hide');
    }
  }
}
</script>

<style lang="scss" scoped>

</style>