<template>
  <div>
    <!-- botones para marcar y eliminar -->
    <div v-if="this.selected">
      <md-button @click="markAsReadAPI(null)"><md-icon>mark_email_read</md-icon> mark as read ({{this.selected}})</md-button>
      <md-button class="md-primary" @click="markAsImportantAPI(null)"><md-icon>grade</md-icon> mark as important ({{this.selected}})</md-button>
      <md-button class="md-accent" @click="sentToTrashAPI"><md-icon>delete</md-icon> move to trash ({{this.selected}})</md-button>
    </div>
    <!-- fin de los botones-->
    <md-list class="md-triple-line">
      <md-progress-bar v-if="!this.loaded" class="md-accent" md-mode="indeterminate"></md-progress-bar>

      <md-empty-state
          v-if="!mails.length && this.loaded"
          class="md-primary"
          md-icon="inbox"
          md-label="Nothing in Inbox"
          md-description="Wait a moment and try again later.">
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

          <div class="md-list-item-text" v-bind:class="{'mark_as_read':!mail.is_read}" @click="viewEmail(index, mail)">
            <span>{{ mail.mail_from }} <span class="md-caption">({{translateDate(mail.created_at)}})</span></span>
            <span>{{ mail.title }}</span>
            <p>{{ mail.body }}</p>
          </div>
          <md-button class="md-icon-button md-list-action" v-on:click="markAsImportantAPI(index)">
            <md-icon v-bind:class="{'md-primary':mail.is_important}">
              {{ mail.is_important ? 'star':'star_border' }}
            </md-icon>
          </md-button>

        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showMarkedSuccessful" md-persistent>
      <span>Email(s) {{markAsImportant? 'marked':'unmarked'}} as important!</span>
    </md-snackbar>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showTrashSuccessful" md-persistent>
      <span>Email(s) sent to trash!</span>
    </md-snackbar>
  </div>
</template>

<script>
import moment from 'moment';
export default {
name: "InboxComponent",
  data: () => ({
    mails:[],
    loaded:false,
    showMarkedSuccessful:false,
    showTrashSuccessful:false,
    markAsImportant:false,
  }),
  mounted() {
    this.getAllEmailsAPI();
  },
  computed:{
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    }
  },
  methods:{
    getAllEmailsAPI(){
      this.loaded = false;
      this.axios.get('http://localhost:8000/mails/inbox').then((response)=>{
        let data = response.data;
        for (let i = 0; i < data.length; i++) {
          data[i].is_selected = false;
          data[i].first_letter = data[i].mail_from.substring(0,1).toUpperCase();
        }
        this.mails = data;
      }).catch((error)=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      })
    },

    markAsImportantAPI(index = null){
      this.loaded = false;
      let data = {
        id_mails:[],
        is_important:true
      };
      if (index == null){
        this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
          data.id_mails.push(mail.id);
        });
      }
      else{
        data.id_mails.push(this.mails[index].id);
        data.is_important = !this.mails[index].is_important;
      }

      this.markAsImportant = data.is_important;

      this.axios.put('http://localhost:8000/mails/mark',data).then(()=>{
        this.showMarkedSuccessful = true;

        if (index!=null)
          this.mails[index].is_important = !this.mails[index].is_important;

        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_important = true;
          mail.is_selected = false;
        });
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },

    markAsReadAPI(index = null){
      this.loaded = false;
      let data = {
        id_mails:[]
      };
      if (index == null){
        this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
          data.id_mails.push(mail.id);
        });
      }
      else{
        data.id_mails.push(this.mails[index].id);
      }


      this.axios.put('http://localhost:8000/mails/read',data).then(()=>{
        //this.showMarkedSuccessful = true;

        if (index!=null)
          this.mails[index].is_read = !this.mails[index].is_read;

        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_read = true;
          mail.is_selected = false;
        });
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },

    sentToTrashAPI(){
      let data = {
        id_mails:[],
        is_deleted:true
      };
      this.loaded = false;
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/trash',data).then(()=>{
        this.showTrashSuccessful = true;
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
        this.loaded = true;
      });
    },

    viewEmail(index, email){
      this.markAsReadAPI(index);
      this.$emit('view',email);
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
  .mark_as_read{
    font-weight: bold;
  }
  .md-list-item-text{
    cursor: pointer;
  }
</style>