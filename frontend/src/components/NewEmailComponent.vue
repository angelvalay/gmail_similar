<template>
  <div>
    <md-button @click="showDialog = true" class="md-fab md-plain md-fixed md-fab-bottom-right">
      <md-icon>edit</md-icon>
    </md-button>
    <md-dialog :md-active.sync="showDialog">
      <md-dialog-title>New email</md-dialog-title>

      <md-dialog-content>
        <md-field>
          <label>Mail to</label>
          <md-input v-model="inputMailTo" required></md-input>
        </md-field>
        <md-field>
          <label>Title</label>
          <md-input v-model="inputTitle"></md-input>
        </md-field>
        <md-field>
          <label>Body</label>
          <md-textarea v-model="inputBody" maxlength="255" required></md-textarea>
        </md-field>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="showDialog = false">Close</md-button>
        <md-button class="md-primary" @click="showDialog = false; showMessageSuccessful = true">Send</md-button>
      </md-dialog-actions>
    </md-dialog>

    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showMessageSuccessful" md-persistent>
      <span>Email sent successful!</span>
    </md-snackbar>
  </div>
</template>

<script>
export default {
name: "NewEmailComponent",
  data:()=>({
    showDialog:false,
    showMessageSuccessful:false,
    inputMailTo:null,
    inputTitle:'',
    inputBody:''
  })
}
</script>

<style lang="scss" scoped>

</style>