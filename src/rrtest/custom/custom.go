package custom

import (
	"fmt"
	"net/http"

	rrttp "github.com/spiral/roadrunner/service/http"
	"github.com/spiral/roadrunner/service/rpc"
)

const ID = "custom"

type Service struct {
}

func (s *Service) Init(rpc *rpc.Service, r *rrttp.Service) (bool, error) {
	r.AddMiddleware(s.middleware)
	rpc.Register("myRpc", &MyRpc{})
	return true, nil
}

func (s *Service) middleware(f http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		pass, ok := r.URL.Query()["password"]
		if !ok || pass[0] != "123" {
			fmt.Fprint(w, "Not allowed")
			return
		}

		fmt.Fprint(w, "Go auth pass\n")

		f(w, r)
	}
}
