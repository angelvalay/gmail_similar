<template>
  <div>
    <!-- botones para marcar y eliminar -->
    <div v-if="this.selected">
      <md-button class="md-primary">Mark to important ({{this.selected}})</md-button>
      <md-button class="md-accent">move to trash ({{this.selected}})</md-button>
    </div>
    <!-- fin de los botones-->
    <md-list class="md-triple-line">

      <!-- lista de correos -->
      <div v-for="(mail,index) in mails" :key="index">
        <md-list-item>
          <md-button class="md-icon-button" v-on:click="mail.is_selected = !mail.is_selected">
            <md-icon>
              {{mail.is_selected ? 'check_box':'check_box_outline_blank'}}
            </md-icon>
          </md-button>
          <div class="md-list-item-text">
            <span>{{ mail.mail_to }}</span>
            <span>{{ mail.title }}</span>
            <p>{{ mail.body }}</p>
          </div>
          <md-button class="md-icon-button md-list-action" v-on:click="mail.is_important = !mail.is_important">
            <md-icon v-bind:class="{'md-primary':mail.is_important}">
              {{ mail.is_important ? 'star':'star_border' }}
            </md-icon>
          </md-button>
        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
  </div>
</template>

<script>
export default {
name: "InboxComponent",
  data: () => ({
    mails:[
      {
        'is_selected':false,
        'id':1,
        'mail_to':'test@example.com',
        'mail_from':null,
        'title':'this is a title',
        'body':'this a body',
        'is_important':true,
        'is_deleted':false
      },
      {
        'is_selected':false,
        'id':2,
        'mail_to':'test@example.com',
        'mail_from':null,
        'title':'this is a title',
        'body':'this a body',
        'is_important':false,
        'is_deleted':false
      },
      {
        'is_selected':false,
        'id':3,
        'mail_to':'test@example.com',
        'mail_from':null,
        'title':'this is a title',
        'body':'this a body',
        'is_important':false,
        'is_deleted':false
      },
      {
        'is_selected':false,
        'id':4,
        'mail_to':'test@example.com',
        'mail_from':null,
        'title':'this is a title',
        'body':'this a body',
        'is_important':false,
        'is_deleted':false
      },
    ]
  }),
  computed:{
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    }
  }
}
</script>

<style scoped>

</style>