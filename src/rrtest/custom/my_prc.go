package custom

type MyRpc struct {
}

func (s *MyRpc) Check(input string, output *string) error {
	if input == "viktor" {
		*output = "admin"
	} else {
		*output = "regular customer"
	}

	return nil
}
