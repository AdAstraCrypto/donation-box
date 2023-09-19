jQuery(document).ready(function ($) {
  // var tipButton = document.querySelector('.cdbbc-tip-button');
  var tipButton = document.querySelectorAll(".cdbbc-tip-button");
  var emailInput = $("#client_email");

  // attach an event listener to the email input
  emailInput.on("blur", function () {
    // get the email value
    var email = emailInput.val();
    var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (!emailRegex.test(email)) {
      alert(wallets_data.const_msg.valid_email);
    }
  });

  // var messageEl = document.querySelectorAll('.message')
  if (tipButton) {
    for (var i = 0; i < tipButton.length; i++) {
      tipButton[i].addEventListener("click", function () {
        let coin_symbol = $(this).data("symbol");

        var user_account = $(this).data("metamask-address");
        var price = $(this).data("metamask-amount");
        let contract_addres = $(this).data("metamask-contract");
        let Selected_chain = $(this).data("chain");
        // Let's imagine you want to receive an ether tip
        const yourAddress = user_account;

        price = price != "" ? price : "0.006"; // an ether has 18 decimals, here in hex.
        const desiredNetwork = Selected_chain; // '1' is the Ethereum main network ID.

        // Detect whether the current browser is ethereum-compatible,
        // and handle the case where it isn't:
        if (
          typeof window.ethereum === "undefined" ||
          typeof web3 === "undefined"
        ) {
          const el = document.createElement("div");
          el.innerHTML =
            "<a href='https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn?hl=en' target='_blank'>Click Here </a> to install MetaMask extention";

          Swal.fire({
            title: "MetaMask extention need to be installed",
            html: el,
            icon: "warning",
          });
        } else {
          if (ethereum.selectedAddress == undefined) {
            Swal.fire({
              text: "Please wait while connection establish",
              didOpen: () => {
                Swal.showLoading();
              },

              allowOutsideClick: false,
            });

            const accounts = ethereum
              .request({ method: "eth_requestAccounts" })
              .then(function (accounts) {
                Swal.close();
              });
          }
          // In the case they approve the log-in request, you'll receive their accounts:
          else {
            // You also should verify the user is on the correct network:
            console.log(desiredNetwork);
            if (ethereum.chainId != desiredNetwork) {
              try {
                Swal.fire({
                  // title: extradata.const_msg.required_network,
                  text: "You have not selected the required network,Click ok to switch the network",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  confirmButtonText: "Ok",
                  reverseButtons: true,
                }).then((result) => {
                  if (result.isConfirmed) {
                    cdbbc_change_network(desiredNetwork);
                  }
                });
              } catch (switchError) {}
            } else {
              Swal.fire({
                title: `Confirm amount in ${coin_symbol.toUpperCase()}`,
                //  html: confirm_payment ,
                // icon: "warning",
                allowOutsideClick: false,
                html: `<input type="text" value="${price}" class="swal2-input" id="donation_amount" placeholder="Enter amount">`,
                preConfirm: () => {
                  const donation_amount =
                    Swal.getPopup().querySelector("#donation_amount").value;
                  if (!donation_amount) {
                    Swal.showValidationMessage(`Please Enter Amount`);
                  }

                  return { donation_amount: donation_amount };
                },
                showCancelButton: true,
                confirmButtonText: "Confirm",
                reverseButtons: true,
              }).then((result) => {
                //  console.log(result)
                if (result.isConfirmed) {
                  ethereum
                    .request({ method: "eth_requestAccounts" })
                    .catch(function (reason) {})
                    .then(function (accounts) {
                      // In the case they approve the log-in request, you'll receive their accounts:
                      const account = accounts[0];
                      sendEtherFrom(
                        account,
                        result.value,
                        coin_symbol,
                        contract_addres,
                        function (err, transaction) {
                          if (err) {
                            return;
                          }
                        }
                      );
                    });
                }
              });
            }
          }
        }

        function sendEtherFrom(account, price, symbol, address, callback) {
          const method = "eth_sendTransaction";
          const parameters = [
            {
              from: account,
              to: yourAddress,
              value: ethers.utils.parseEther(price.donation_amount)._hex,
              gas: "0xa028",
            },
          ];
          const from = account;

          // Now putting it all together into an RPC request:
          const payload = {
            method: method,
            params: parameters,
            from: from,
          };
          let send_token_amount = price.donation_amount;
          let to_address = yourAddress; //extradata.recever)
          let contract_address = address;
          window.ethersProvider = new ethers.providers.Web3Provider(
            window.ethereum
          );
          let default_currency = ["eth", "bnb"];
          if (contract_address != "") {
            cdbbc_send_token(contract_address, send_token_amount, to_address);
          } else {
            try {
              const provider = new ethers.providers.Web3Provider(
                window.ethereum,
                "any"
              );
              const signer = provider.getSigner();
              var secret_code = "";
              const tx = {
                from: from,
                to: yourAddress,
                value: ethers.utils.parseEther(price.donation_amount)._hex,
                gasLimit: ethers.utils.hexlify("0x5208"), // 21000
              };

              const trans = signer
                .sendTransaction(tx)
                .then(async function (res) {
                  Swal.close();

                  Swal.fire({
                    title: "Transaction in Process ! Please Wait ",
                    //   imageUrl: extradata.url + "/assets/images/metamask.png",
                    //   footer: process_messsage,
                    didOpen: () => {
                      Swal.showLoading();
                    },
                    allowOutsideClick: false,
                  });

                  return res.wait();
                })
                .then(function (tx) {
                  Swal.close();
                  Swal.fire({
                    title: "Transaction Completed Successfully !",
                    icon: "success",
                    timer: 2000,
                  });
                  try {
                    // If popup was open, close it
                    document
                      .querySelector("#donatewallets>.close-modal")
                      .click();
                  } catch (e) {}
                })
                .catch(function (error) {
                  if (error.code == "4001") {
                    Swal.close();
                    Swal.fire({
                      title: "Transaction Rejected",
                      icon: "error",
                      timer: 2000,
                    });

                    return;
                  } else if (error.code == "-32602") {
                    Swal.close();
                    Swal.fire({
                      title: "Invalid Receiver Address",
                      icon: "error",
                      timer: 10000,
                    });
                    return;
                  } else {
                    Swal.close();
                    Swal.fire({
                      text: error,
                      icon: "error",
                      timer: 10000,
                    });
                    return;
                  }
                });
            } catch (erro) {
              console.log(erro);
              Swal.close();
              Swal.fire({
                text: erro,
                icon: "error",
                timer: 2000,
              });
              return;
            }
          }
        }
      });
    }
  }
});
//Change metamask network if not on desired network
async function cdbbc_change_network(chain_id) {
  let ethereum = window.ethereum;
  const data = cpmw_chain_data(chain_id);
  try {
    const chain_change = await ethereum.request({
      method: "wallet_switchEthereumChain",
      params: [{ chainId: chain_id }],
    });
    // location.reload();
  } catch (switchError) {
    // This error code indicates that the chain has not been added to MetaMask.
    if (switchError.code === 4902) {
      try {
        ethereum.request({
          method: "wallet_addEthereumChain",
          params: data,
        });
      } catch (addError) {
        // handle "add" error
      }
    }
    // handle other "switch" errors
  }
}

//Add binance chain
function cpmw_chain_data(chain_id) {
  if (chain_id == "0x38") {
    const data = [
      {
        chainId: "0x38",
        chainName: "Binance Smart Chain",
        nativeCurrency: {
          name: "BNB",
          symbol: "BNB",
          decimals: 18,
        },
        rpcUrls: ["https://bsc-dataseed.binance.org/"],
        blockExplorerUrls: ["https://bscscan.com/"],
      },
    ];
    return data;
  }
  if (chain_id == "0x61") {
    const data = [
      {
        chainId: "0x61",
        chainName: "Binance Smart Chain Testnet",
        nativeCurrency: {
          name: "BNB",
          symbol: "BNB",
          decimals: 18,
        },
        rpcUrls: ["https://data-seed-prebsc-2-s3.binance.org:8545/"],
        blockExplorerUrls: ["https://testnet.bscscan.com"],
      },
    ];
    return data;
  }
}
//Send Tokens
async function cdbbc_send_token(
  contract_address,
  send_token_amount,
  to_address
) {
  if (contract_address) {
    // The ERC-20 ABI
    try {
      var abi = [
        "function name() view returns (string)",
        "function symbol() view returns (string)",
        "function gimmeSome() external",
        "function balanceOf(address _owner) public view returns (uint256 balance)",
        "function transfer(address _to, uint256 _value) public returns (bool success)",
        "function decimals() view returns (uint256)",
      ];
      const provider = new ethers.providers.Web3Provider(
        window.ethereum,
        "any"
      );
      await provider.send("eth_requestAccounts", []);
      const signer = provider.getSigner();
      let userAddress = await signer.getAddress();
      var address = contract_address;
      var contract = new ethers.Contract(address, abi, signer);
      var secret_code = "";
      // Listen for Transfer events (triggered after the transaction)
      contract.ontransfer = function (from, to, amount) {
        var text = ethers.utils.formatEther(amount);
      };
      const decimals = await contract.decimals();
      // Get the balance of the wallet before the transfer
      var targetAddress = to_address;
      var amount = ethers.utils.parseUnits(send_token_amount, decimals);
      let befyblc = await contract
        .balanceOf(userAddress)
        .then(function (balance) {
          var text = ethers.utils.formatUnits(balance, decimals);
          if (Number(text) >= send_token_amount) {
            contract
              .transfer(targetAddress, amount)
              .then(function (tx) {
                Swal.close();

                Swal.fire({
                  title: "Transaction in Process ! Please Wait ",
                  //   imageUrl: extradata.url + "/assets/images/metamask.png",
                  //   footer: process_messsage,
                  didOpen: () => {
                    Swal.showLoading();
                  },
                  allowOutsideClick: false,
                });

                return tx.wait();
              })
              .then(function (tx) {
                // Get the balance of the provider after the transfer
                contract.balanceOf(userAddress).then(function (balance) {
                  var text = ethers.utils.formatUnits(balance, 18);
                  // console.log(tx);
                  Swal.close();
                  Swal.fire({
                    title: "Transaction completed successfully !",
                    icon: "success",
                    timer: 2000,
                  });
                  try {
                    // If popup was open, close it
                    document
                      .querySelector("#donatewallets>.close-modal")
                      .click();
                  } catch (e) {}
                });
              })
              .catch(function (error) {
                if (error.code == "4001") {
                  Swal.close();
                  Swal.fire({
                    title: "Transaction Rejected",
                    icon: "error",
                    timer: 2000,
                  });

                  return;
                } else if (error.code == "-32602") {
                  Swal.close();
                  Swal.fire({
                    title: "Invalid Receiver Address",
                    icon: "error",
                    timer: 10000,
                  });
                  return;
                } else {
                  Swal.close();
                  Swal.fire({
                    text: error,
                    icon: "error",
                    timer: 10000,
                  });
                  return;
                }
              });
          } else {
            Swal.close();
            Swal.fire({
              title: "Insufficient Balance" + text,
              icon: "error",
            });
          }
        });
    } catch (error) {
      console.log(error);
      Swal.close();
      Swal.fire({
        text: error,
        icon: "error",
        timer: 2000,
      });
      return;
    }
  }
}

/*
|--------------------------------------------------------------------------
|  Copy to cliboard & tab change funtion
|--------------------------------------------------------------------------
*/
jQuery(document).ready(function ($) {
  $("button.cdbbc_btn").click(function () {
    let current_input = $(this).prev().val();
    navigator.clipboard.writeText(current_input);
    $(this).prev().select();
  });

  $(".cdbbc-container ul.cdbbc-tabs li").click(function () {
    var random_id = $(this).attr("data-random");

    var tab_id = $(this).attr("data-tab");
    $(".cdbbc-tab-rand" + random_id + " ul.cdbbc-tabs li").removeClass(
      "current"
    );
    $(".cdbbc-tab-rand" + random_id + " .cdbbc-tabs-content").removeClass(
      "current"
    );
    $(this).addClass("current");
    $("#" + tab_id).addClass("current");
  });
});
