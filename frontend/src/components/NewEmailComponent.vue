<template>
  <div>
    <md-dialog :md-active.sync="show">
      <md-dialog-title>Send new email</md-dialog-title>
      <form @submit.prevent="addNewEmailAPI">
        <md-dialog-content>
            <md-field>
              <label>Mail to</label>
              <md-input type="email" v-model="inputMailTo" required></md-input>
            </md-field>
            <md-field>
              <label>Title</label>
              <md-input v-model="inputTitle" maxlength="50" required></md-input>
            </md-field>
            <md-field>
              <label>Body</label>
              <md-textarea v-model="inputBody" maxlength="255" required></md-textarea>
            </md-field>
        </md-dialog-content>

        <md-dialog-actions>
          <md-button class="md-primary" @click="hideDialog">Close</md-button>
          <md-button class="md-primary" type="submit">Send</md-button>
        </md-dialog-actions>
      </form>
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
  data:()=>({
    show:false,
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
        this.axios.post('http://'+location.hostname+':8000/mails',data).then(response=>{
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
    showDialog(){
      this.show = true;
    },
    hideDialog(){
      this.show = false;
    }
  }
}
</script>

<style lang="scss" scoped>

</style>