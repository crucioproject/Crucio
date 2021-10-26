import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../../services/api.service';
import { AuthService } from '../../../services/auth.service';
import { Crucio } from '../../main';

@Component({
  selector: 'app-author-comments',
  templateUrl: './author-comments.component.html',
  styleUrls: ['./author-comments.component.scss']
})
export class AuthorCommentsComponent implements OnInit {
  commentsByQuestion: any;
  private readonly user: Crucio.User;
  commentSearch: any;
  distinctAuthors: any;

  constructor(private api: ApiService, private auth: AuthService) {
    this.user = this.auth.getUser();

    this.commentSearch = { };
  }

  ngOnInit() {
    this.loadComments();
  }

  loadComments(): void {
    const data = {
      query: this.commentSearch.query,
      limit: 100,
    };
    this.api.get('comments/author', data).subscribe(result => {
      const comments = result.comments;

      this.commentsByQuestion = [];
      for (const c of comments) {
        let already = false;
        for (const cq of this.commentsByQuestion) {
          if (c.question === cq.question) {
            cq.list.push(c);
            already = true;
            break;
          }
        }

        if (!already) {
          this.commentsByQuestion.push({ question: c.question, list: [ c ] });
        }
      }

      this.commentsByQuestion.sort((a, b) => b.list[0].date - a.list[0].date);
    });
  }
}
