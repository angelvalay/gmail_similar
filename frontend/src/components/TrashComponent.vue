<template>
  <div>
    <!-- botones para marcar y eliminar -->
    <div v-if="this.selected">
      <md-button class="md-primary" @click="sendToInboxAPI"><md-icon>move_to_inbox</md-icon> move to inbox ({{this.selected}})</md-button>
      <!-- todo falta agregar un boton para eliminar definitivamente los correos-->
    </div>
    <!-- fin de los botones-->
    <md-list class="md-triple-line">
      <md-progress-bar v-if="!this.loaded" class="md-accent" md-mode="indeterminate"></md-progress-bar>

      <md-empty-state
          v-if="!mails.length && this.loaded"
          class="md-primary"
          md-icon="delete_outline"
          md-label="Nothing in Trash"
          md-description="The trash is empty.">
      </md-empty-state>

      <!-- lista de correos -->
      <div v-for="(mail,index) in mails" :key="index">
        <md-list-item>
          <md-button class="md-icon-button" v-on:click="mail.is_selected = !mail.is_selected">
            <md-icon>
              {{mail.is_selected ? 'check_box':'check_box_outline_blank'}}
            </md-icon>
          </md-button>

          <md-avatar class="md-avatar-icon">{{ mail.first_letter }}</md-avatar>

          <div class="md-list-item-text">
            <span>{{ mail.mail_to ? 'me' : mail.mail_from }} <span class="md-caption">({{translateDate(mail.created_at)}})</span></span>
            <span>{{ mail.title }}</span>
            <p>{{ mail.body }}</p>
          </div>

        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showSentInboxSuccessful" md-persistent>
      <span>Email(s) sent to trash!</span>
    </md-snackbar>
  </div>
</template>

<script>
import moment from 'moment';
export default {
name: "TrashComponent",
  data: () => ({
    mails:[],
    loaded:false,
    showSentInboxSuccessful:false,
  }),
  computed:{
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    }
  },
  mounted() {
    this.getEmailsTrashAPI();
  },
  methods:{
    getEmailsTrashAPI(){
      this.loaded = false;
      this.axios.get('http://localhost:8000/mails/trash').then((response)=>{
        let data = response.data;
        for (let i = 0; i < data.length; i++) {
          data[i].is_selected = false;
          let mail = data[i].mail_to ? 'me' : data[i].mail_from;
          data[i].first_letter = mail.substring(0,1).toUpperCase();
        }
        this.mails = data;
      }).catch((error)=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      })
    },
    sendToInboxAPI(){
      let data = {
        id_mails:[],
        is_deleted:false
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/trash',data).then(()=>{
        this.showSentInboxSuccessful = true;
        let lastIndexSelected = 0;
        while(lastIndexSelected > -1){
          lastIndexSelected = this.mails.findIndex(mail=>mail.is_selected);
          if (lastIndexSelected === -1)
            break;
          this.mails.splice(lastIndexSelected,1);
        }
      }).catch(error=>{
        console.log(error);
      }).then(()=>{

      });
    },
    translateDate(date){
      return moment(date).fromNow();
    }
  }
}
</script>

<style scoped>
.md-avatar-icon{
  margin-right: 15px !important;

}
</style>